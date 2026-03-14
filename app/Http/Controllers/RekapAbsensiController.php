<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Only Direktur and Admin can access
        if (!Auth::user()->hasAnyRole(['direktur', 'admin'])) {
            abort(403);
        }

        // Default to current month
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Get all users (employees)
        $users = User::with('profile.division', 'profile.position')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'direktur');
            })
            ->orderBy('name')
            ->get();

        // Get attendance for the month
        $attendances = Absensi::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('user_id');

        // Build recap data
        $rekapData = $users->map(function ($user) use ($attendances, $daysInMonth, $startDate) {
            $userAttendances = $attendances->get($user->id, collect());

            $hadir = $userAttendances->filter(fn($a) => $a->status === 'closed' || $a->status === 'auto_closed')->count();
            $izin = 0; // Can be integrated with PermohonanIzin later
            $sakit = 0;
            $alpha = 0;

            // Calculate working days (exclude weekends)
            $workingDays = 0;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $startDate->copy()->day($day);
                if (!$date->isWeekend()) {
                    $workingDays++;
                }
            }

            $alpha = max(0, $workingDays - $hadir - $izin - $sakit);

            // Total worked hours
            $totalMinutes = $userAttendances->sum('worked_minutes');
            $totalHours = round($totalMinutes / 60, 1);

            return (object) [
                'user' => $user,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total_hours' => $totalHours,
                'working_days' => $workingDays,
            ];
        });

        return view('rekap_absensi.index', [
            'rekapData' => $rekapData,
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
