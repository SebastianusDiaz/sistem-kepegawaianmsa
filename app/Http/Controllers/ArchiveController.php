<?php

namespace App\Http\Controllers;

use App\Models\FileArchive;
use App\Services\ArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    protected $archiveService;

    public function __construct(ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Restrict access to Admin and Direktur
        if (!Auth::user()->hasAnyRole(['admin', 'direktur'])) {
            abort(403, 'Akses ditolak. Hanya Admin dan Direktur yang dapat mengakses halaman ini.');
        }

        $query = FileArchive::with('user');

        // Filter: Only show 'Assignment Document', 'Updated Press Release', 'Kerjasama Document', and 'Direct Upload'
        $query->whereIn('source', ['Assignment Document', 'Updated Press Release', 'Kerjasama Document', 'Direct Upload']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%");
            });
        }

        $archives = $query->latest()->paginate(10);

        return view('archives.index', compact('archives'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $this->archiveService->store($file, 'Direct Upload', Auth::id());

        return redirect()->route('archives.index')
            ->with('success', 'File berhasil diarsipkan.');
    }

    /**
     * Download the specified file.
     */
    public function download(FileArchive $archive)
    {
        if (!Storage::disk('public')->exists($archive->file_path)) {
            return back()->with('error', 'File fisik tidak ditemukan.');
        }

        return Storage::disk('public')->download($archive->file_path, $archive->original_name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FileArchive $archive)
    {
        // Optional: Add policy check here

        $this->archiveService->delete($archive);

        return redirect()->route('archives.index')
            ->with('success', 'File berhasil dihapus dari arsip.');
    }
}
