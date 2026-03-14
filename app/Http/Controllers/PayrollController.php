<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Models\Absensi; // Assuming Absensi model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Payroll::with('user');

        if ($user->hasAnyRole(['direktur', 'admin'])) {
            // Direktur & Admin see all, filters available
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);
            $query->whereYear('period_end', $year)
                ->whereMonth('period_end', $month);
        } else {
            // Employee sees only their own
            $query->where('user_id', $user->id);
            // Optional: restricted view, maybe not filtered by default to show history?
            // Let's keep the filter capability for them too if they want
            if ($request->has('month') && $request->has('year')) {
                $query->whereYear('period_end', $request->year)
                    ->whereMonth('period_end', $request->month);
            }
        }

        $payrolls = $query->latest()->paginate(10);
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return view('payrolls.index', compact('payrolls', 'month', 'year'));
    }

    public function create()
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        // Filter: Include Pegawai and Editor.
        // This implicitly INCLUDES "Wartawan" if they also have "Pegawai" role.
        // We explicitly exclude Direktur and Admin from the list.
        $employees = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['pegawai', 'editor']);
        })->whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['direktur', 'admin']);
        })->get();

        return view('payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'base_salary' => 'required|numeric|min:0',
        ]);

        $startDate = \Carbon\Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Check for duplicates
        $exists = Payroll::where('user_id', $request->user_id)
            ->whereBetween('period_end', [$startDate, $endDate])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Payroll for this user and period already exists.');
        }

        // --- Automated Deductions Calculation ---
        $calc = $this->calculateAutoDeductions($request->user_id, $startDate, $endDate, $request->base_salary);
        $deductionDetails = $calc['details'];
        $totalDeductionAmount = $calc['total'];

        Payroll::create([
            'user_id' => $request->user_id,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'base_salary' => $request->base_salary,
            'allowances' => 0,
            'bonus' => 0,
            'deductions' => $totalDeductionAmount,
            'deduction_details' => $deductionDetails,
            'net_salary' => $request->base_salary - $totalDeductionAmount,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('payrolls.index')->with('success', 'Payroll created with automated deductions.');
    }

    public function show(Payroll $payroll)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['direktur', 'admin']) && $user->id !== $payroll->user_id) {
            abort(403);
        }

        $payroll->load('user.profile.division', 'user.profile.position');
        return view('payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        // Edit is likely unnecessary if we do everything in Show, but we can keep standard flow.
        // For now, redirect to show as "Manage" page.
        return redirect()->route('payrolls.show', $payroll);
    }

    public function update(Request $request, Payroll $payroll)
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        // Logic to update Bonuses/Deductions items
        // Expecting specific action or full update

        $payroll->base_salary = $request->input('base_salary', $payroll->base_salary);

        // Example: Add Item logic could be separate, but let's handle generic update
        // Use separate methods for clear actions usually better, but let's see.
        // If the request comes from the "Manage" page form:

        if ($request->has('add_item')) {
            $details = $payroll->deduction_details ?? [];
            $newItem = [
                'name' => $request->item_name,
                'amount' => $request->item_amount,
                'type' => $request->item_type // 'bonus' or 'deduction'
            ];
            // We might want to store bonuses and deductions separately in JSON or just one array
            // Model has 'deductions' column as total. 'bonus' column as total. 'deduction_details' as JSON.
            // Let's reuse 'deduction_details' to store BOTH for flexibility or add 'bonus_details'?
            // Schema only has deduction_details. I'll put everything there with a 'type' key.

            $details[] = $newItem;
            $payroll->deduction_details = $details;
        }

        if ($request->has('remove_item_index')) {
            $details = $payroll->deduction_details ?? [];
            $index = $request->remove_item_index;
            if (isset($details[$index])) {
                array_splice($details, $index, 1);
            }
            $payroll->deduction_details = $details;
        }

        // Re-calculate Totals
        $totalBonus = 0;
        $totalDeduction = 0;
        $details = $payroll->deduction_details ?? [];

        foreach ($details as $item) {
            if (($item['type'] ?? '') === 'bonus') {
                $totalBonus += $item['amount'];
            } elseif (($item['type'] ?? '') === 'deduction') {
                $totalDeduction += $item['amount'];
            }
        }

        $payroll->bonus = $totalBonus;
        $payroll->deductions = $totalDeduction;
        $payroll->net_salary = $payroll->base_salary + $payroll->bonus - $payroll->deductions; // + allowances if any

        $payroll->save();

        return back()->with('success', 'Payroll updated.');
    }

    // Specical method for adding items cleanly
    public function addItem(Request $request, Payroll $payroll)
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:bonus,deduction'
        ]);

        $details = $payroll->deduction_details ?? [];
        $details[] = [
            'name' => $request->name,
            'amount' => $request->amount,
            'type' => $request->type
        ];
        $payroll->deduction_details = $details;
        $this->recalculate($payroll);

        return back()->with('success', 'Item added.');
    }

    public function removeItem(Request $request, Payroll $payroll)
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        $index = $request->index;
        $details = $payroll->deduction_details ?? [];

        if (isset($details[$index])) {
            array_splice($details, $index, 1);
            $payroll->deduction_details = $details;
            $this->recalculate($payroll);
            return back()->with('success', 'Item removed.');
        }

        return back()->with('error', 'Item not found.');
    }

    private function recalculate(Payroll $payroll)
    {
        $totalBonus = 0;
        $totalDeduction = 0;
        $details = $payroll->deduction_details ?? [];

        foreach ($details as $item) {
            if (($item['type'] ?? '') === 'bonus') {
                $totalBonus += $item['amount'];
            } elseif (($item['type'] ?? '') === 'deduction') {
                $totalDeduction += $item['amount'];
            }
        }

        $payroll->bonus = $totalBonus;
        $payroll->deductions = $totalDeduction;
        $payroll->net_salary = $payroll->base_salary + $payroll->bonus - $payroll->deductions;
        $payroll->save();
    }

    public function markAsPaid(Payroll $payroll)
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        if ($payroll->status === 'paid') {
            return back()->with('info', 'Payroll is already marked as paid.');
        }

        $payroll->update(['status' => 'paid']);
        return back()->with('success', 'Status has been updated to Paid.');
    }

    public function destroy(Payroll $payroll)
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted.');
    }

    public function print(Payroll $payroll)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['direktur', 'admin']) && $user->id !== $payroll->user_id) {
            abort(403);
        }

        if ($payroll->status === 'draft') {
            return back()->with('error', 'Slip gaji tidak dapat dicetak karena status masih Draft.');
        }

        $payroll->load('user.profile.division', 'user.profile.position');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payrolls.pdf', compact('payroll'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Slip-Gaji-' . $payroll->user->name . '-' . $payroll->period_end->format('M-Y') . '.pdf');
    }

    public function syncDeductions(Payroll $payroll)
    {
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        if ($payroll->status === 'paid') {
            return back()->with('error', 'Cannot sync deductions for paid payroll.');
        }

        $calc = $this->calculateAutoDeductions($payroll->user_id, $payroll->period_start, $payroll->period_end, $payroll->base_salary);

        // Strategy: Keep manual items (those NOT starting with "Potongan Izin/Sakit/Cuti")
        // and replace the automated ones.

        $currentDetails = $payroll->deduction_details ?? [];
        $manualDetails = array_filter($currentDetails, function ($item) {
            $name = $item['name'] ?? '';
            return !str_starts_with($name, 'Potongan Izin')
                && !str_starts_with($name, 'Potongan Sakit')
                && !str_starts_with($name, 'Potongan Cuti');
        });

        $newDetails = array_merge($manualDetails, $calc['details']);

        $payroll->deduction_details = array_values($newDetails);
        $this->recalculate($payroll);

        return back()->with('success', 'Payroll deductions synchronized with approved leaves.');
    }

    private function calculateAutoDeductions($userId, $startDate, $endDate, $baseSalary)
    {
        $permissions = \App\Models\PermohonanIzin::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
            })
            ->get();

        // Nominal Potongan diset Fixed (100rb per hari) sesuai kebijakan
        // TODO: Bisa dibuat dinamis via database jika perlu
        $rates = [
            'izin' => 100000,
            'cuti' => 100000,
            'sakit' => 100000
        ];

        $daysCount = ['izin' => 0, 'sakit' => 0, 'cuti' => 0];

        foreach ($permissions as $perm) {
            $start = $perm->tanggal_mulai < $startDate ? $startDate : $perm->tanggal_mulai;
            $end = $perm->tanggal_selesai > $endDate ? $endDate : $perm->tanggal_selesai;
            $days = $start->diffInDays($end) + 1;

            if ($days > 0) {
                $type = strtolower($perm->jenis_izin);
                // Map permission types if necessary, currently matching keys
                if (isset($daysCount[$type])) {
                    $daysCount[$type] += $days;
                }
            }
        }

        $details = [];
        $totalAmount = 0;

        foreach ($daysCount as $type => $days) {
            if ($days > 0) {
                $rate = $rates[$type] ?? 100000;
                $amount = $days * $rate;
                $totalAmount += $amount;
                $details[] = [
                    'name' => 'Potongan ' . ucfirst($type) . ' (' . $days . ' hari)',
                    'amount' => $amount,
                    'type' => 'deduction'
                ];
            }
        }

        return ['details' => $details, 'total' => $totalAmount];
    }
}
