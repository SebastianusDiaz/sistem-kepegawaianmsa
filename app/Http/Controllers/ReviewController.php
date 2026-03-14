<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentDiscussion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    protected $archiveService;

    public function __construct(\App\Services\ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    public function index()
    {
        // Show assignments that are 'submitted' or 'revision'?
        // Usually editors review 'submitted' tasks.
        $assignments = Assignment::with(['reporter', 'editor'])
            ->whereIn('status', ['submitted', 'revision'])
            ->latest()
            ->get();

        return view('reviews.index', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['reporter', 'discussions.user', 'attachments']);
        return view('reviews.show', compact('assignment'));
    }

    public function store(Request $request, Assignment $assignment)
    {
        $request->validate([
            'action' => 'required|in:approve,revision,comment',
            'message' => 'required_if:action,revision,comment|nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $user = Auth::user();

        // Save discussion/comment if message exists
        if ($request->filled('message') || $request->hasFile('file')) {
            $filePath = null;
            if ($request->hasFile('file')) {
                // Should use ArchiveService ideally, but for now simple storage or service usage
                $path = $request->file('file')->store('discussions', 'public');
                $filePath = $path;
            }

            AssignmentDiscussion::create([
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
                'message' => $request->message ?? '(File Attachment)',
                'file_path' => $filePath,
                'type' => $request->action === 'revision' ? 'revision_request' : 'comment',
            ]);
        }

        if ($request->action === 'approve') {
            $updateData = [
                'status' => 'published',
                'editor_id' => Auth::id() // CLAIM: Reviewer gets credit
            ];

            // Handle Final Document Replacement
            if ($request->hasFile('final_document')) {
                $archive = $this->archiveService->store(
                    $request->file('final_document'),
                    'Final Press Release',
                    Auth::id()
                );

                $updateData['evidence_document'] = 'storage/' . $archive->file_path;

                // Save to history
                $assignment->attachments()->create([
                    'file_path' => 'storage/' . $archive->file_path,
                    'file_type' => 'document_final',
                    'mime_type' => $request->file('final_document')->getMimeType(),
                    'size' => $request->file('final_document')->getSize(),
                ]);
            }

            // Handle Final Link Update
            if ($request->filled('final_link')) {
                $updateData['evidence_link'] = $request->final_link;
            }

            $assignment->update($updateData);

            if ($assignment->reporter && $assignment->reporter->email) {
                \Illuminate\Support\Facades\Mail::to($assignment->reporter->email)->send(new \App\Mail\AssignmentStatusUpdated($assignment, 'Assignment Anda telah dipublish.'));
            }

            return redirect()->back()->with('success', 'Assignment approved and published.');
        }

        if ($request->action === 'revision') {
            $assignment->update([
                'status' => 'revision',
                'editor_id' => Auth::id() // CLAIM: Reviewer gets credit for reviewing
            ]);

            if ($assignment->reporter && $assignment->reporter->email) {
                \Illuminate\Support\Facades\Mail::to($assignment->reporter->email)->send(new \App\Mail\AssignmentStatusUpdated($assignment, $request->message ?? 'Mohon segera direvisi.'));
            }

            return redirect()->back()->with('warning', 'Assignment returned for revision.');
        }

        return back()->with('success', 'Comment added.');
    }
}
