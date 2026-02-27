<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('pages.auth.signup', ['title' => 'Pendaftaran Akun']);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'numeric', 'unique:users,nik'],
            'nomor_wa' => ['required', 'numeric', 'unique:users,nomor_wa'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'id_plans' => ['required', 'integer', 'in:1,2'], // 1: Anak Kos, 2: Pemilik Kos
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'plan_type' => ['nullable', 'string', 'required_if:id_plans,2'],
            'package_type' => ['nullable', 'string', 'required_if:plan_type,premium'],
            'jumlah_kamar' => ['nullable', 'integer', 'min:1'],
        ]);

        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'nik' => $request->nik,
                'nomor_wa' => $request->nomor_wa,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'id_plans' => $request->id_plans,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Role & Plan Logic simplified
            if ($request->id_plans == 1) {
                // Anak Kos
                $user->assignRole('users'); // Match web.php role name
            } else {
                // Pemilik Kos (Admin)
                $user->assignRole('admin');

                // Mapping Plan Type to id_plans
                $planType = $request->plan_type;

                $map = [
                    'pro' => 2,
                    'premium' => 3,
                    'premium_perkamar' => 4,
                    'pro_perkamar' => 5
                ];

                $user->id_plans = $map[$planType] ?? 2;

                // Sync with Spatie Roles (to match permission matrix)
                // Role names in matrix: pro, premium, per_kamar_pro, per_kamar_premium
                $roleMap = [
                    'pro' => 'pro',
                    'premium' => 'premium',
                    'pro_perkamar' => 'per_kamar_pro',
                    'premium_perkamar' => 'per_kamar_premium'
                ];

                if (isset($roleMap[$planType])) {
                    $user->assignRole($roleMap[$planType]);
                }

                $user->save();

                // Create Langganan Record for Owners
                $langgananNames = [
                    'pro' => 'MEMBER PRO',
                    'premium' => 'MEMBER PREMIUM',
                    'pro_perkamar' => 'PER KAMAR PRO',
                    'premium_perkamar' => 'PER KAMAR PREMIUM'
                ];

                if (isset($langgananNames[$planType])) {
                    $jenis = \App\Models\JenisLangganan::where('nama', $langgananNames[$planType])->first();
                    if ($jenis) {
                        \App\Models\Langganan::create([
                            'id_user' => $user->id,
                            'id_langganan' => $jenis->id,
                            'jumlah_kamar' => $request->jumlah_kamar ?? 0,
                            'status' => 'active', // Default active for trial/initial
                            'tanggal_pembayaran' => now(),
                        ]);
                    }
                }
            }

            return $user;
        });

        event(new Registered($user));

        // Membuang user kembali ke halaman login dengan menyertakan session pemberitahuan sukses
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login masuk ke akun Anda.');
    }
}
