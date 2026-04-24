<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Simpan rincian PushSubscription dari frontend.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required'
        ]);

        $endpoint = $request->endpoint;
        $token = $request->input('keys.auth');
        $key = $request->input('keys.p256dh');
        $user = auth()->user();

        if ($user) {
            $user->updatePushSubscription($endpoint, $key, $token);
            return response()->json(['success' => true], 200);
        }

        return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
    }
}
