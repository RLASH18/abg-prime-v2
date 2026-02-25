<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesFileUploads
{
    /**
     * Store an uploaded file with a custom filename
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $prefix
     * @return string
     */
    protected function storeFile(UploadedFile $file, string $directory, ?string $prefix = null): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $filename = $prefix
            ? "{$prefix}_{$timestamp}.{$extension}"
            : "{$timestamp}.{$extension}";

        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * Delete a file from storage
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    protected function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }
}
