<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FonnteService;

class FonnteController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Kirim pesan percobaan.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'target' => 'required',
            'message' => 'required'
        ]);

        $response = $this->fonnteService->sendMessage(
            $request->target,
            $request->message
        );

        return response()->json($response);
    }

    /**
     * Metode contoh untuk mengirim OTP (dapat dipanggil dari controller lain)
     */
    public function sendOtp($target, $otp)
    {
        $message = "Kode OTP Anda adalah: *{$otp}*\n\nBerlaku selama 1 menit. Mohon tidak memberikan kode ini kepada siapapun.";

        return $this->fonnteService->sendMessage($target, $message);
    }

    /**
     * Metode contoh untuk mengirim pengingat batas waktu (tenggat)
     */
    public function sendDeadlineReminder($target, $name, $kosName, $dueDate)
    {
        $message = "Halo *{$name}*,\n\nIni adalah pengingat bahwa masa sewa kos Anda di *{$kosName}* akan segera berakhir pada tanggal *{$dueDate}*.\n\nSilakan lakukan pembayaran melalui aplikasi untuk memperpanjang masa sewa. Terima kasih!";

        return $this->fonnteService->sendMessage($target, $message);
    }
}
