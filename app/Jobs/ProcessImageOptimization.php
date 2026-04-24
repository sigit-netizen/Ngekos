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
     * Jumlah detik pekerjaan dapat berjalan sebelum waktu habis (timeout).
     */
    public $timeout = 300; // 5 minutes

    /**
     * Buat instance job baru (Create a new job instance).
     */
    public function __construct($tempPath, $directory, Model $model, $field = 'foto')
    {
        $this->tempPath = $tempPath;
        $this->directory = $directory;
        $this->model = $model;
        $this->field = $field;
    }

    /**
     * Eksekusi job (Execute the job).
     */
    public function handle(): void
    {
        ini_set('memory_limit', '512M'); // Tingkatkan memori untuk gambar besar
        
        // 1. Periksa apakah file sementara (temp file) ada
        if (!Storage::disk('public')->exists($this->tempPath)) {
            return;
        }

        $fullPath = storage_path('app/public/' . $this->tempPath);

        // Ambil jalur file lama sebelum memperbarui (updating)
        $oldFile = $this->model->{$this->field};

        // 2. Proses dan optimalkan (Process and optimize)
        $newPath = ImageHelper::uploadAndOptimize($fullPath, $this->directory);

        // 3. Perbarui Model (Update Model)
        $this->model->update([
            $this->field => $newPath
        ]);

        // 4. Hapus file sementara (Delete temp file)
        Storage::disk('public')->delete($this->tempPath);

        // 5. Hapus file gambar lama yang sudah dioptimalkan (old optimized image)
        if ($oldFile) {
            $oldPath = str_replace('storage/', '', $oldFile);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
    }
}
