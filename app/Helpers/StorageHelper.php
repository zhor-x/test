<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageHelper
{
    /**
     * Upload a file to the public disk with a generated name.
     *
     * @param $file
     * @param string $folder
     * @return array|null
     */
    public static function uploadFile( $file, string $folder = 'Uploads'): ?array
    {
        // Handle UploadedFile
        if ($file instanceof UploadedFile) {
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::uuid()->toString() . '.' . $extension;
            $filePath = $file->storeAs($folder, $fileName, 'public');
        }
        // Handle file path
        elseif (is_string($file) && file_exists($file)) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = Str::uuid()->toString() . '.' . $extension;
            $filePath = Storage::disk('public')->putFileAs($folder, new \Illuminate\Http\File($file), $fileName);
        } else {
            return null;
        }

        if (!$filePath) {
            return null;
        }

        return [
            'file_name' => $fileName,
            'url' => Storage::url($filePath),
            'path' => $filePath,
        ];
    }

    /**
     * Delete a file from the public disk.
     *
     * @param string $fileUrl
     * @return bool
     */
    public static function deleteFile(string $fileUrl): bool
    {
        $filePath = str_replace('/storage/', '', parse_url($fileUrl, PHP_URL_PATH));
        return Storage::disk('public')->delete($filePath);
    }

    /**
     * Get the URL of a file.
     *
     * @param string $filePath
     * @return string
     */
    public static function getFileUrl(string $filePath): string
    {
        return Storage::url($filePath);
    }
}
