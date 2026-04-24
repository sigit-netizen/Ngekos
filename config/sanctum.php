<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Domain Stateful (Stateful Domains)
    |--------------------------------------------------------------------------
    |
    | Permintaan dari domain / host berikut akan menerima cookie autentikasi
    | API stateful. Biasanya, ini harus mencakup domain lokal
    | dan produksi Anda yang mengakses API Anda melalui SPA frontend.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : '',
        env('FRONTEND_URL') ? ','.parse_url(env('FRONTEND_URL'), PHP_URL_HOST) : ''
    ))),

    /*
    |--------------------------------------------------------------------------
    | Guard Sanctum (Sanctum Guards)
    |--------------------------------------------------------------------------
    |
    | Array ini berisi guard autentikasi yang akan diperiksa ketika
    | Sanctum mencoba mengautentikasi permintaan. Jika tidak ada dari guard ini
    | yang dapat mengautentikasi permintaan, Sanctum akan menggunakan bearer
    | token yang ada pada permintaan masuk untuk autentikasi.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Menit Kedaluwarsa (Expiration Minutes)
    |--------------------------------------------------------------------------
    |
    | Nilai ini mengontrol jumlah menit hingga token yang dikeluarkan akan
    | dianggap kedaluwarsa. Ini akan menimpa nilai apa pun yang disetel dalam atribut
    | "expires_at" token, tetapi sesi pihak pertama tidak terpengaruh.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Middleware Sanctum (Sanctum Middleware)
    |--------------------------------------------------------------------------
    |
    | Saat mengautentikasi SPA pihak pertama Anda dengan Sanctum, Anda mungkin perlu
    | menyesuaikan beberapa middleware yang digunakan Sanctum saat memproses
    | permintaan. Anda dapat mengubah middleware yang tercantum di bawah ini sesuai kebutuhan.
    |
    */

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],

];
