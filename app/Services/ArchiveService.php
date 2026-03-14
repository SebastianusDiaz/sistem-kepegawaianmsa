<?php

namespace App\Services;

use App\Models\FileArchive;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchiveService
{
    /**
     * Store an uploaded file in the archive.
     *
     * @param UploadedFile $file
     * @param string $source Context of upload
     * @param int|null $userId
     * @return FileArchive
     */
    public function store(UploadedFile $file, string $source, ?int $userId = null): FileArchive
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate a unique stored name
        $storedName = Str::uuid() . '.' . $extension;

        // Store physical file
        $path = $file->storeAs('archives', $storedName, 'public');

        // Create DB record
        return FileArchive::create([
            'user_id' => $userId,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'file_path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
            'source' => $source,
        ]);
    }

    /**
     * Delete a file from archive.
     *
     * @param FileArchive $archive
     * @return bool
     */
    public function delete(FileArchive $archive): bool
    {
        // Delete physical file
        if (Storage::disk('public')->exists($archive->file_path)) {
            Storage::disk('public')->delete($archive->file_path);
        }

        // Delete DB record
        return $archive->delete();
    }

    /**
     * Archive an existing file path (for publishing workflow).
     *
     * @param string $existingPath
     * @param string $source
     * @param int|null $userId
     * @param string|null $category
     * @return FileArchive|null
     */
    public function archiveFromPath(string $existingPath, string $source, ?int $userId = null, ?string $category = null): ?FileArchive
    {
        // Remove 'storage/' prefix if present
        $cleanPath = Str::after($existingPath, 'storage/');

        if (!Storage::disk('public')->exists($cleanPath)) {
            return null;
        }

        $mimeType = Storage::disk('public')->mimeType($cleanPath);
        $size = Storage::disk('public')->size($cleanPath);
        $originalName = basename($cleanPath);

        return FileArchive::create([
            'user_id' => $userId,
            'original_name' => $originalName,
            'stored_name' => $originalName,
            'file_path' => $cleanPath,
            'mime_type' => $mimeType,
            'size' => $size,
            'source' => $source,
            'category' => $category,
        ]);
    }
}
