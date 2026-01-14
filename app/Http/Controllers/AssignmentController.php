<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Kerjasama;
use App\Models\User;
use App\Actions\HandleFieldAttendanceAction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;


class AssignmentController extends Controller
{
    protected $archiveService;

    public function __construct(\App\Services\ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    public function index()
    {
        $user = Auth::user();

        // Open assignments (no reporter)
        $openAssignments = Assignment::whereNull('reporter_id')
            ->whereIn('status', ['assigned', 'draft'])
            ->latest()
            ->get();

        if ($user->hasRole('wartawan')) {
            $assignments = Assignment::where('reporter_id', $user->id)
                ->orderByRaw("FIELD(status, 'assigned', 'accepted', 'on_site', 'draft', 'submitted', 'published', 'canceled')")
                ->latest()
                ->get();
        } else {
            // Editor/Admin sees all
            $assignments = Assignment::with(['reporter', 'editor'])->latest()->get();
        }

        return view('assignments.index', compact('assignments', 'openAssignments'));
    }

    public function create()
    {
        $reporters = User::role('wartawan')->get();
        $activeKerjasamas = Kerjasama::active()->get();
        return view('assignments.create', compact('reporters', 'activeKerjasamas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'reporter_id' => 'nullable|exists:users,id',
            'kerjasama_id' => 'nullable|exists:kerjasamas,id',
            'start_time' => 'required|date',
            'deadline' => 'required|date',
            'location_name' => 'required',
        ]);

        Assignment::create([
            'editor_id' => Auth::id(),
            'reporter_id' => $request->reporter_id,
            'kerjasama_id' => $request->kerjasama_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'location_name' => $request->location_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'start_time' => $request->start_time,
            'deadline' => $request->deadline,
            'status' => 'assigned',
            'priority' => $request->priority ?? 'normal',
        ]);

        return redirect()->route('assignments.index')->with('success', 'Penugasan berhasil dibuat.');
    }

    public function show(Assignment $assignment)
    {
        return view('assignments.show', compact('assignment'));
    }

    public function updateStatus(Request $request, Assignment $assignment, HandleFieldAttendanceAction $attendanceAction)
    {
        $request->validate([
            'status' => 'required|in:accepted,on_site,submitted',
        ]);

        try {
            DB::beginTransaction();

            if ($request->status === 'on_site') {
                // Removed location enforcement
                $attendanceAction->execute(
                    $assignment,
                    $request->lat ?? 0,
                    $request->lng ?? 0,
                    $request->accuracy ?? 0
                );
            }

            if ($request->status === 'submitted') {
                $request->validate([
                    'evidence_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                    'evidence_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                    'evidence_link' => 'nullable|string'
                ]);

                // Fallback validation: At least one must be present (New or Existing)
                $hasNewEvidence = $request->hasFile('evidence_photo') || $request->hasFile('evidence_document') || $request->filled('evidence_link');
                $hasExistingEvidence = $assignment->evidence_photo || $assignment->evidence_document || $assignment->evidence_link;

                if (!$hasNewEvidence && !$hasExistingEvidence) {
                    throw ValidationException::withMessages(['evidence' => 'Harap lampirkan minimal satu bukti (Foto, Dokumen, atau Link).']);
                }
            }

            $updateData = ['status' => $request->status];

            $updateData = ['status' => $request->status];

            if ($request->hasFile('evidence_photo')) {
                $archive = $this->archiveService->store(
                    $request->file('evidence_photo'),
                    'Assignment Photo',
                    Auth::id()
                );
                $updateData['evidence_photo'] = 'storage/' . $archive->file_path;
            }

            if ($request->hasFile('evidence_document')) {
                $archive = $this->archiveService->store(
                    $request->file('evidence_document'),
                    'Assignment Document',
                    Auth::id()
                );
                $updateData['evidence_document'] = 'storage/' . $archive->file_path;
            }

            if ($request->filled('evidence_link')) {
                $updateData['evidence_link'] = $request->evidence_link;
            } elseif ($request->has('evidence_link') && $assignment->status === 'submitted') {
                // Allow clearing link in edit mode if key is present but empty
                $updateData['evidence_link'] = null;
            }

            $assignment->update($updateData);

            DB::commit();

            $msg = $request->status === 'submitted'
                ? 'Bukti liputan berhasil diupload dan status diperbarui menjadi Submitted.'
                : 'Status penugasan berhasil diperbarui.';

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function take(Assignment $assignment)
    {
        if ($assignment->reporter_id) {
            return back()->withErrors(['error' => 'Penugasan ini sudah diambil oleh wartawan lain provided.']);
        }

        $assignment->update([
            'reporter_id' => Auth::id(),
            'status' => 'accepted' // Auto-accept when taking
        ]);

        return redirect()->route('assignments.show', $assignment)->with('success', 'Anda telah mengambil penugasan ini.');
    }

    public function exportPdf(Assignment $assignment)
    {
        // Permission check: Only the assigned reporter or admin/editor can export
        // For simplicity, we allow any logged-in user who can view it, or restrict similarly to show()
        // Here we restrict to the assigned reporter OR if user has permission to manage assignments
        $user = Auth::user();

        if ($assignment->reporter_id !== $user->id && !$user->hasRole(['editor', 'admin', 'direktur', 'pegawai'])) {
            abort(403);
        }

        $pdf = Pdf::loadView('assignments.pdf', compact('assignment'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Surat-Tugas-' . $assignment->slug . '.pdf');
    }

    public function respond(Request $request, Assignment $assignment)
    {
        $user = Auth::user();

        // Check permission (editor, admin, pegawai)
        if (!$user->hasRole(['editor', 'admin', 'pegawai'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk memberikan respon.');
        }

        $request->validate([
            'staff_response_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            'staff_response_note' => 'required|string',
        ]);

        $updateData = [
            'staff_response_note' => $request->staff_response_note,
            'staff_response_at' => now(),
        ];

        if ($request->hasFile('staff_response_file')) {
            $archive = $this->archiveService->store(
                $request->file('staff_response_file'),
                'Staff Response',
                $user->id
            );
            $updateData['staff_response_file'] = 'storage/' . $archive->file_path;
        }

        $assignment->update($updateData);

        return back()->with('success', 'Respon berhasil dikirim.');
    }
}
