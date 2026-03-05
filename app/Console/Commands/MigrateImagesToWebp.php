<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MigrateImagesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'images:migrate-to-webp';

    /**
     * The console command description.
     */
    protected $description = 'Migrate all images from public/images to public/storage and convert them to .webp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceDir = public_path('images');
        $targetDir = storage_path('app/public');

        if (!File::exists($sourceDir)) {
            $this->error("The source directory {$sourceDir} does not exist.");
            return;
        }

        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $files = File::allFiles($sourceDir);
        $manager = new ImageManager(new Driver());
        $count = 0;

        foreach ($files as $file) {
            $relativePath = $file->getRelativePath();
            $filename = $file->getFilenameWithoutExtension();
            $extension = strtolower($file->getExtension());

            // Build target path
            $targetSubPath = $targetDir . ($relativePath ? '/' . $relativePath : '');
            if (!File::exists($targetSubPath)) {
                File::makeDirectory($targetSubPath, 0755, true);
            }

            // Only convert image formats (excluding svg/gif which may break or not be supported optimally by this exact method without checks)
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $targetFile = $targetSubPath . '/' . $filename . '.webp';

                try {
                    $image = $manager->read($file->getPathname());
                    $encoded = $image->toWebp(75);
                    File::put($targetFile, (string) $encoded);
                    $this->info("Converted and moved: {$relativePath}/{$filename}.{$extension} -> {$relativePath}/{$filename}.webp");
                    $count++;
                } catch (\Exception $e) {
                    $this->error("Failed to convert {$file->getPathname()}: " . $e->getMessage());
                }
            } else {
                // For SVG or other files, just copy them directly
                $targetFile = $targetSubPath . '/' . $file->getFilename();
                File::copy($file->getPathname(), $targetFile);
                $this->info("Copied without conversion: {$relativePath}/{$file->getFilename()}");
                $count++;
            }
        }

        $this->info("Successfully migrated {$count} files from public/images to public/storage.");
    }
}
