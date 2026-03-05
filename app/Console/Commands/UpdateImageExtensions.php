<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\User;
use App\Models\Transaksi;

class UpdateImageExtensions extends Command
{
    protected $signature = 'db:update-image-extensions';
    protected $description = 'Update image records in DB to use .webp extension';

    public function handle()
    {
        $this->info("Updating Kamar...");
        $kamars = Kamar::all();
        foreach ($kamars as $kamar) {
            if ($kamar->foto && !str_ends_with($kamar->foto, '.webp')) {
                $kamar->foto = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $kamar->foto);
                $kamar->save();
            }
        }

        $this->info("Updating Kos...");
        $koses = Kos::all();
        foreach ($koses as $kos) {
            if ($kos->foto && !str_ends_with($kos->foto, '.webp')) {
                $kos->foto = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $kos->foto);
                $kos->save();
            }
        }

        $this->info("Updating User...");
        $users = User::all();
        foreach ($users as $user) {
            if ($user->foto_profil && !str_ends_with($user->foto_profil, '.webp')) {
                $user->foto_profil = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $user->foto_profil);
                $user->save();
            }
        }

        $this->info("Updating Transaksi...");
        $transaksis = Transaksi::all();
        foreach ($transaksis as $transaksi) {
            if ($transaksi->bukti_pembayaran && !str_ends_with($transaksi->bukti_pembayaran, '.webp')) {
                $transaksi->bukti_pembayaran = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $transaksi->bukti_pembayaran);
                $transaksi->save();
            }
        }

        $this->info("Done updating DB records!");
    }
}
