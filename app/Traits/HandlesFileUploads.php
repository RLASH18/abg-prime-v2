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
        if (!$path) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }

    /**
     * Handle multiple file uploads with optional deletion of old files
     *
     * @param array $files
     * @param array $fieldNames
     * @param string $directory
     * @param array|null $oldPaths
     * @return array
     */
    protected function handleMultipleFileUploads(
        array $files,
        array $fieldNames,
        string $directory,
        ?array $oldPaths = null
    ): array {
        $uploadedPaths = [];

        foreach ($fieldNames as $field) {
            if (isset($files[$field]) && $files[$field] instanceof UploadedFile) {
                // Delete old file if exists
                if ($oldPaths && isset($oldPaths[$field])) {
                    $this->deleteFile($oldPaths[$field]);
                }

                // Store new file
                $uploadedPaths[$field] = $this->storeFile($files[$field], $directory, $field);
            }
        }

        return $uploadedPaths;
    }
}
