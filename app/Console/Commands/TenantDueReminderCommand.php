<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaksi;
use App\Notifications\RentDueReminderNotification;
use Carbon\Carbon;

class TenantDueReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:due-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send rent due reminders to tenants 1 day before due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for tenants with rent due tomorrow...');

        // Find users who are tenants (have id_kamar)
        $tenants = User::whereNotNull('id_kamar')->get();

        foreach ($tenants as $tenant) {
            // Get latest paid rent/booking transaction
            $lastRent = Transaksi::where('id_user', $tenant->id)
                ->where('id_kamar', $tenant->id_kamar)
                ->where('status', 'paid')
                ->latest()
                ->first();

            if ($lastRent && $lastRent->jatuh_tempo) {
                $nowWib = now('Asia/Jakarta')->startOfDay();
                $dueWib = Carbon::parse($lastRent->jatuh_tempo)->timezone('Asia/Jakarta')->startOfDay();
                $diffDays = $nowWib->diffInDays($dueWib, false);

                // Check if stay is > 7 days (based on created_at and jatuh_tempo)
                $createdAt = Carbon::parse($lastRent->created_at)->timezone('Asia/Jakarta')->startOfDay();
                $stayDuration = $createdAt->diffInDays($dueWib, false);

                $shouldNotify = false;
                $prefix = "";

                if ($diffDays === 1) {
                    $shouldNotify = true;
                    $prefix = "H-1 ";
                } elseif ($diffDays === 3 && $stayDuration > 7) {
                    $shouldNotify = true;
                    $prefix = "H-3 ";
                }

                if ($shouldNotify) {
                    $this->info("Notifying {$tenant->name} (Room: {$tenant->id_kamar}) - $prefix");
                    
                    $tenant->notify(new RentDueReminderNotification([
                        'nama' => $tenant->name,
                        'kos' => $tenant->kosAnak ? $tenant->kosAnak->nama_kos : 'Kos Anda',
                        'kamar' => $tenant->kamar ? $tenant->kamar->nomor_kamar : '-',
                        'jatuh_tempo' => $dueWib->format('d-m-Y'),
                        'prefix' => $prefix
                    ]));
                }
            }
        }

        $this->info('Reminder process complete.');
    }
}
