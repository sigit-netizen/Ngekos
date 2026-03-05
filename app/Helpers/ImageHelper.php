<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Upload and optimize image.
     * 
     * @param mixed $file
     * @param string $directory
     * @param int|null $width
     * @return string Path to the optimized image
     */
    public static function uploadAndOptimize($file, $directory = 'uploads', $width = 1200)
    {
        // Increase execution time for heavy image processing (compression/convert)
        set_time_limit(180); // 3 minutes

        // 1. Initialize ImageManager with GD driver
        $manager = new ImageManager(new Driver());

        // 2. Read the image (Intervention 3 supports file path, string, or UploadedFile)
        $image = $manager->read($file);

        // 3. Resize/Scale if larger than $width
        if ($image->width() > $width) {
            $image->scale(width: $width);
        }

        // 4. Generate unique filename with .webp extension
        $filename = Str::random(40) . '.webp';
        $path = $directory . '/' . $filename;

        // 5. Encode to WebP with 75% quality
        $encoded = $image->toWebp(75);

        // 6. Save to disk (public)
        Storage::disk('public')->put($path, (string) $encoded);

        return 'storage/' . $path;
    }
}
