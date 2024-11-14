<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageUploadTrait
{
    /**
     * Check if the given file is a valid image file.
     *
     * @param mixed $file
     * @return bool
     */
    public function isValidImageFile($file): bool
    {
        if (!$file instanceof UploadedFile) {
            return false;
        }

        return true;
    }

    /**
     * Upload an image file.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @param string|null $filename
     * @return string
     */
    public function uploadImage(UploadedFile $file, string $folder = 'images', string $disk = 'public'): string
    {
        $filename = $filename ?? Str::random(40);
        $filename = Str::replace(' ', '-', $filename);

        $extension = $file->getClientOriginalExtension();

        $path = $file->storeAs(
            $folder,
            $filename . '.' . $extension,
            $disk
        );

        return $path;
    }

    /**
     * Delete an image file.
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function deleteImage(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Update an image file.
     *
     * @param UploadedFile $file
     * @param string $oldPath
     * @param string $folder
     * @param string $disk
     * @param string|null $filename
     * @return string
     */
    public function updateImage(UploadedFile $file, string $oldPath, string $folder = 'images', string $disk = 'public', string $filename = null): string
    {
        // Delete the old image
        $this->deleteImage($oldPath, $disk);

        // Upload the new image
        return $this->uploadImage($file, $folder, $disk, $filename);
    }
}
