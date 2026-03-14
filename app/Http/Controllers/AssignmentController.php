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

        if ($user->hasRole('wartawan') && !$user->hasAnyRole(['admin', 'editor', 'direktur', 'pegawai'])) {
            // Wartawan sees ALL their tasks (including submitted/revision for tracking)
            $assignments = Assignment::where('reporter_id', $user->id)
                ->orderByRaw("FIELD(status, 'assigned', 'accepted', 'on_site', 'revision', 'submitted', 'published', 'canceled')")
                ->latest()
                ->get();
        } else {
            // Editor/Admin/Pegawai: show active tasks + own revision tasks
            $assignments = Assignment::with(['reporter', 'editor'])
                ->where(function ($query) use ($user) {
                    $query->whereNotIn('status', ['submitted', 'revision', 'published'])
                        ->orWhere(function ($q) use ($user) {
                            // Show own assignments needing revision so reporter can fix & resubmit
                            $q->where('reporter_id', $user->id)
                                ->where('status', 'revision');
                        });
                })
                ->latest()
                ->get();
        }

        return view('assignments.index', compact('assignments', 'openAssignments'));
    }

    public function published()
    {
        $user = Auth::user();

        if ($user->hasRole('wartawan') && !$user->hasAnyRole(['admin', 'editor', 'direktur', 'pegawai'])) {
            $assignments = Assignment::where('reporter_id', $user->id)
                ->where('status', 'published')
                ->latest()
                ->get();
        } else {
            $assignments = Assignment::with(['reporter', 'editor'])
                ->where('status', 'published')
                ->latest()
                ->get();
        }

        return view('assignments.published', compact('assignments'));
    }

    public function create()
    {
        $reporters = User::role('wartawan')->with('profile.position')->get();
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

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'location_name' => $request->location_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'start_time' => $request->start_time,
            'deadline' => $request->deadline,
            'priority' => $request->priority ?? 'normal',
            'kerjasama_id' => $request->kerjasama_id,
        ];

        if (Auth::user()->hasRole('wartawan')) {
            // Self-Service Logic
            $data['reporter_id'] = Auth::id();
            $data['editor_id'] = null; // Open until an editor picks it or reviews it
            $data['status'] = 'accepted'; // Auto-accept
        } else {
            // Admin/Editor Logic
            $data['reporter_id'] = $request->reporter_id;
            // $data['editor_id'] = Auth::id(); // REMOVED: Creation does not equal Editorship/Review Performance
            $data['editor_id'] = null; // Let it be claimed via Review
            $data['status'] = 'assigned';
        }

        $assignment = Assignment::create($data);

        // Send Email Notification
        if ($assignment->reporter_id) {
            // Direct Assignment
            if ($assignment->reporter->email) {
                \Illuminate\Support\Facades\Mail::to($assignment->reporter->email)->send(new \App\Mail\AssignmentAssigned($assignment));
            }
        } else {
            // Open Assignment - Notify all reporters
            $reporters = User::role('wartawan')->whereNotNull('email')->get();
            foreach ($reporters as $reporter) {
                \Illuminate\Support\Facades\Mail::to($reporter->email)->send(new \App\Mail\AssignmentAvailable($assignment));
            }
        }

        return redirect()->route('assignments.index')->with('success', 'Penugasan berhasil dibuat.');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['discussions.user', 'reporter', 'editor', 'attachments']);
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
                // Document is now REQUIRED
                $documentPresent = $request->hasFile('evidence_document') || $assignment->evidence_document;
                if (!$documentPresent) {
                    throw ValidationException::withMessages(['evidence_document' => 'Bahan Press Release (Dokumen) wajib diupload.']);
                }

                $request->validate([
                    'evidence_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                    'evidence_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                    'evidence_link' => 'nullable|string'
                ]);
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

                // Save to history
                $assignment->attachments()->create([
                    'file_path' => 'storage/' . $archive->file_path,
                    'file_type' => 'photo',
                    'mime_type' => $request->file('evidence_photo')->getMimeType(),
                    'size' => $request->file('evidence_photo')->getSize(),
                ]);
            }

            if ($request->hasFile('evidence_document')) {
                $archive = $this->archiveService->store(
                    $request->file('evidence_document'),
                    'Assignment Document',
                    Auth::id()
                );
                $updateData['evidence_document'] = 'storage/' . $archive->file_path;

                // Save to history
                $assignment->attachments()->create([
                    'file_path' => 'storage/' . $archive->file_path,
                    'file_type' => 'document',
                    'mime_type' => $request->file('evidence_document')->getMimeType(),
                    'size' => $request->file('evidence_document')->getSize(),
                ]);
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

            if ($request->status === 'submitted') {
                // Notify Editor
                $editor = $assignment->editor;
                if ($editor && $editor->email) {
                    \Illuminate\Support\Facades\Mail::to($editor->email)->send(new \App\Mail\AssignmentSubmitted($assignment));
                } else {
                    // Fallback: Notify all editors/admins if no specific editor assigned
                    $admins = User::role(['admin', 'editor'])->whereNotNull('email')->get();
                    foreach ($admins as $admin) {
                        \Illuminate\Support\Facades\Mail::to($admin->email)->send(new \App\Mail\AssignmentSubmitted($assignment));
                    }
                }
            }

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
        $user = Auth::user();

        if ($assignment->reporter_id !== $user->id && !$user->hasRole(['editor', 'admin', 'direktur', 'pegawai'])) {
            abort(403);
        }

        $assignment->load(['reporter.profile', 'editor.profile']);

        // Fetch Director for signature
        $director = User::role('direktur')->first();

        $pdf = Pdf::loadView('assignments.pdf', compact('assignment', 'director'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Surat-Tugas-' . $assignment->slug . '.pdf');
    }

    public function respond(Request $request, Assignment $assignment)
    {
        $user = Auth::user();

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

    public function updateEvidence(Request $request, Assignment $assignment)
    {
        // Allow updating evidence link/document even after published
        // Restricted to Admin/Editor/Director/Pegawai
        if (!Auth::user()->hasRole(['admin', 'editor', 'direktur', 'pegawai'])) {
            abort(403);
        }

        $request->validate([
            'evidence_link' => 'nullable|url',
            'evidence_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $updateData = [];

        if ($request->filled('evidence_link')) {
            $updateData['evidence_link'] = $request->evidence_link;
        }

        if ($request->hasFile('evidence_document')) {
            $archive = $this->archiveService->store(
                $request->file('evidence_document'),
                'Updated Press Release',
                Auth::id()
            );
            $updateData['evidence_document'] = 'storage/' . $archive->file_path;

            // Save to history
            $assignment->attachments()->create([
                'file_path' => 'storage/' . $archive->file_path,
                'file_type' => 'document_updated',
                'mime_type' => $request->file('evidence_document')->getMimeType(),
                'size' => $request->file('evidence_document')->getSize(),
            ]);
        }

        if (!empty($updateData)) {
            $assignment->update($updateData);
            return back()->with('success', 'Data publikasi berhasil diperbarui.');
        }

        return back()->with('info', 'Tidak ada perubahan data.');
    }
}
