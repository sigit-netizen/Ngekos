<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Tangani permintaan tautan (link) reset kata sandi yang masuk.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Kami akan mengirimkan tautan reset kata sandi ke pengguna ini. Setelah kami mencoba
        // mengirim tautan tersebut, kami akan memeriksa responsnya kemudian melihat pesan
        // yang perlu kami tampilkan kepada pengguna. Akhirnya, kami akan mengirimkan respons yang sesuai.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }
}
