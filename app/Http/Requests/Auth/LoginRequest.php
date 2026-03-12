<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::select('id', 'email', 'password', 'name')
            ->with('roles')
            ->where('email', $this->email)
            ->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey(), 86400);
            $remaining = RateLimiter::remaining($this->throttleKey(), 5);
            $message = 'Email tidak terdaftar.';
            if ($remaining === 1) {
                $message = 'Email tidak terdaftar. Hati-hati, sisa 1 kesempatan lagi!';
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        if (!\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey(), 86400);

            $remaining = RateLimiter::remaining($this->throttleKey(), 5);
            $message = 'Password salah.';
            if ($remaining === 1) {
                $message = 'Password salah. Hati-hati, sisa 1 kesempatan lagi!';
            }

            throw ValidationException::withMessages([
                'password' => $message,
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $hours = ceil($seconds / 3600);

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Akun ditangguhkan selama 24 jam. Silakan coba lagi dalam $hours jam.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
