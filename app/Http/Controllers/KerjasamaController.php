<?php

namespace App\Http\Controllers;

use App\Models\Kerjasama;
use App\Models\User;
use App\Services\ArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KerjasamaController extends Controller
{
    protected $archiveService;

    public function __construct(ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    public function index(Request $request)
    {
        $query = Kerjasama::with('pic');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kerjasamas = $query->latest()->paginate(10);

        return view('kerjasama.index', compact('kerjasamas'));
    }

    public function create()
    {
        $employees = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'pegawai', 'wartawan', 'editor']);
        })->get();

        return view('kerjasama.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'representative_name' => 'required|string|max:255',
            'representative_phone' => 'nullable|string|max:20',
            'representative_email' => 'nullable|email|max:255',
            'pic_id' => 'required|exists:users,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $request->only([
            'company_name',
            'start_date',
            'end_date',
            'representative_name',
            'representative_phone',
            'representative_email',
            'pic_id'
        ]);
        $data['status'] = 'pending';

        // Handle file upload
        if ($request->hasFile('file')) {
            $archive = $this->archiveService->store(
                $request->file('file'),
                'Kerjasama Document',
                Auth::id()
            );
            $data['file_path'] = $archive->file_path;
        }

        Kerjasama::create($data);

        return redirect()->route('kerjasama.index')->with('success', 'Kerjasama berhasil dibuat dan menunggu persetujuan Direktur.');
    }

    public function show(Kerjasama $kerjasama)
    {
        $kerjasama->load(['pic', 'assignments.reporter', 'approver']);

        return view('kerjasama.show', compact('kerjasama'));
    }

    public function edit(Kerjasama $kerjasama)
    {
        $employees = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'pegawai', 'wartawan', 'editor']);
        })->get();

        return view('kerjasama.edit', compact('kerjasama', 'employees'));
    }

    public function update(Request $request, Kerjasama $kerjasama)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'representative_name' => 'required|string|max:255',
            'representative_phone' => 'nullable|string|max:20',
            'representative_email' => 'nullable|email|max:255',
            'pic_id' => 'required|exists:users,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $request->only([
            'company_name',
            'start_date',
            'end_date',
            'representative_name',
            'representative_phone',
            'representative_email',
            'pic_id'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $archive = $this->archiveService->store(
                $request->file('file'),
                'Kerjasama Document',
                Auth::id()
            );
            $data['file_path'] = $archive->file_path;
        }

        $kerjasama->update($data);

        return redirect()->route('kerjasama.show', $kerjasama)->with('success', 'Kerjasama berhasil diperbarui.');
    }

    public function destroy(Kerjasama $kerjasama)
    {
        $kerjasama->delete();
        return redirect()->route('kerjasama.index')->with('success', 'Kerjasama berhasil dihapus.');
    }

    // Approval Actions (Director Only)
    public function approve(Kerjasama $kerjasama)
    {
        if (!Auth::user()->hasRole('direktur')) {
            abort(403, 'Hanya Direktur yang dapat menyetujui kerjasama.');
        }

        $kerjasama->update([
            'status' => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => null,
        ]);

        return back()->with('success', 'Kerjasama berhasil disetujui dan sekarang aktif.');
    }

    public function reject(Request $request, Kerjasama $kerjasama)
    {
        if (!Auth::user()->hasRole('direktur')) {
            abort(403, 'Hanya Direktur yang dapat menolak kerjasama.');
        }

        $request->validate(['rejection_note' => 'required|string']);

        $kerjasama->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => $request->rejection_note,
        ]);

        return back()->with('success', 'Kerjasama telah ditolak.');
    }
}
