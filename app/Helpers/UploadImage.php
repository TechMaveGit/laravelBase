<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload and process the image.
     *
     * @param UploadedFile $image
     * @param string $folder
     * @param int|null $width
     * @param int|null $height
     * @return string
     */
    public function upload(UploadedFile $image, string $folder, ?int $width = null, ?int $height = null): string
    {
        // Validate image type and size here

        // Resize image if needed
        if ($width || $height) {
            $image = $this->resizeImage($image, $width, $height);
        }

        // Generate a unique file name
        $fileName = $this->generateFileName($image);

        // Store the image
        $path = $image->storeAs($folder, $fileName, 'public');

        return $path;
    }

    /**
     * Resize the given image.
     *
     * @param UploadedFile $image
     * @param int|null $width
     * @param int|null $height
     * @return UploadedFile
     */
    protected function resizeImage(UploadedFile $image, ?int $width, ?int $height): UploadedFile
    {
        $resizedImage = Image::make($image)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->stream();

        // Update the file in the UploadedFile instance
        $image->size = $resizedImage->getSize();
        $image->path = $resizedImage->detach();

        return $image;
    }

    /**
     * Generate a unique file name for the image.
     *
     * @param UploadedFile $image
     * @return string
     */
    protected function generateFileName(UploadedFile $image): string
    {
        $extension = $image->getClientOriginalExtension();
        return Str::uuid() . '.' . $extension;
    }

    // Add more methods as needed, like type checking and size validation

    /**
     * Check if the image already exists in the specified folder.
     *
     * @param string $path
     * @return bool
     */
    public function imageExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }


    /**
     * Delete image
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool
    {
        if ($this->imageExists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
