<?php

namespace App\Jobs;

use App\Helpers\ImageHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class ProcessImageOptimization implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath;
    protected $directory;
    protected $model;
    protected $field;

    /**
     * Create a new job instance.
     */
    public function __construct($tempPath, $directory, Model $model, $field = 'foto')
    {
        $this->tempPath = $tempPath;
        $this->directory = $directory;
        $this->model = $model;
        $this->field = $field;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Check if temp file exists
        if (!Storage::disk('public')->exists($this->tempPath)) {
            return;
        }

        $fullPath = storage_path('app/public/' . $this->tempPath);

        // Capture old file path before updating
        $oldFile = $this->model->{$this->field};

        // 2. Process and optimize
        $newPath = ImageHelper::uploadAndOptimize($fullPath, $this->directory);

        // 3. Update Model
        $this->model->update([
            $this->field => $newPath
        ]);

        // 4. Delete temp file
        Storage::disk('public')->delete($this->tempPath);

        // 5. Delete old optimized image file
        if ($oldFile) {
            $oldPath = str_replace('storage/', '', $oldFile);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
    }
}
