<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Driver Sesi Default (Default Session Driver)
    |--------------------------------------------------------------------------
    |
    | Opsi ini mengontrol "driver" sesi default yang akan digunakan pada
    | permintaan. Secara default, kami akan menggunakan driver asli yang ringan tetapi
    | Anda dapat menentukan driver hebat lainnya yang disediakan di sini.
    |
    | Didukung: "file", "cookie", "database", "apc",
    |            "memcached", "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Masa Hidup Sesi (Session Lifetime)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan jumlah menit yang Anda inginkan agar sesi
    | diizinkan tetap diam sebelum kedaluwarsa. Jika Anda ingin sesi
    | segera kedaluwarsa saat browser ditutup, atur opsi tersebut.
    |
    */

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    /*
    |--------------------------------------------------------------------------
    | Enkripsi Sesi (Session Encryption)
    |--------------------------------------------------------------------------
    |
    | Opsi ini memungkinkan Anda untuk dengan mudah menentukan bahwa semua data sesi Anda
    | harus dienkripsi sebelum disimpan. Semua enkripsi akan dijalankan
    | secara otomatis oleh Laravel dan Anda dapat menggunakan Sesi seperti biasa.
    |
    */

    'encrypt' => false,

    /*
    |--------------------------------------------------------------------------
    | Lokasi File Sesi (Session File Location)
    |--------------------------------------------------------------------------
    |
    | Saat menggunakan driver sesi asli, kami memerlukan lokasi di mana file sesi
    | dapat disimpan. Default telah dietapkan untuk Anda tetapi lokasi yang
    | berbeda dapat ditentukan. Ini hanya diperlukan untuk sesi file.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Koneksi Database Sesi (Session Database Connection)
    |--------------------------------------------------------------------------
    |
    | Saat menggunakan driver sesi "database" atau "redis", Anda dapat menentukan
    | koneksi yang harus digunakan untuk mengelola sesi ini. Ini harus
    | sesuai dengan koneksi dalam opsi konfigurasi database Anda.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Tabel Database Sesi (Session Database Table)
    |--------------------------------------------------------------------------
    |
    | Saat menggunakan driver sesi "database", Anda dapat menentukan tabel yang
    | harus kami gunakan untuk mengelola sesi. Tentu saja, default yang masuk akal
    | disediakan untuk Anda; namun, Anda bebas untuk mengubahnya sesuai kebutuhan.
    |
    */

    'table' => 'sessions',

    /*
    |--------------------------------------------------------------------------
    | Penyimpanan Cache Sesi (Session Cache Store)
    |--------------------------------------------------------------------------
    |
    | Saat menggunakan salah satu backend sesi yang digerakkan oleh cache dari framework, Anda dapat
    | mencantumkan penyimpanan cache yang harus digunakan untuk sesi ini. Nilai ini
    | harus cocok dengan salah satu "stores" cache yang dikonfigurasi aplikasi.
    |
    | Mempengaruhi: "apc", "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Lotre Pembersihan Sesi (Session Sweeping Lottery)
    |--------------------------------------------------------------------------
    |
    | Beberapa driver sesi harus menyapu lokasi penyimpanan mereka secara manual untuk
    | menyingkirkan sesi lama dari penyimpanan. Berikut adalah peluang hal itu akan
    | terjadi pada permintaan tertentu. Secara default, peluangnya adalah 2 dari 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Nama Cookie Sesi (Session Cookie Name)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mengubah nama cookie yang digunakan untuk mengidentifikasi instans sesi
    | berdasarkan ID. Nama yang ditentukan di sini akan digunakan setiap kali a
    | cookie sesi baru dibuat oleh framework untuk setiap driver.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Jalur Cookie Sesi (Session Cookie Path)
    |--------------------------------------------------------------------------
    |
    | Jalur cookie sesi menentukan jalur untuk mana cookie akan
    | dianggap tersedia. Biasanya, ini adalah jalur akar dari
    | aplikasi Anda, tetapi Anda bebas untuk mengubahnya bila diperlukan.
    |
    */

    'path' => '/',

    /*
    |--------------------------------------------------------------------------
    | Domain Cookie Sesi (Session Cookie Domain)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mengubah domain cookie yang digunakan untuk mengidentifikasi sesi
    | dalam aplikasi Anda. Ini akan menentukan domain mana cookie itu
    | tersedia di aplikasi Anda. Default yang masuk akal telah ditetapkan.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Cookie Hanya HTTPS (HTTPS Only Cookies)
    |--------------------------------------------------------------------------
    |
    | Dengan menyetel opsi ini ke true, cookie sesi hanya akan dikirim kembali
    | ke server jika browser memiliki koneksi HTTPS. Ini akan menjaga
    | agar cookie tidak dikirimkan kepada Anda ketika itu tidak dapat dilakukan dengan aman.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | Hanya Akses HTTP (HTTP Access Only)
    |--------------------------------------------------------------------------
    |
    | Menyetel nilai ini ke true akan mencegah JavaScript mengakses
    | nilai cookie dan cookie hanya akan dapat diakses melalui
    | protokol HTTP. Anda bebas untuk memodifikasi opsi ini jika diperlukan.
    |
    */

    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Cookie Same-Site (Same-Site Cookies)
    |--------------------------------------------------------------------------
    |
    | Opsi ini menentukan bagaimana perilaku cookie Anda saat permintaan lintas situs
    | terjadi, dan dapat digunakan untuk memitigasi serangan CSRF. Secara default, kami
    | akan menyetel nilai ini ke "lax" karena ini adalah nilai default yang aman.
    |
    | Didukung: "lax", "strict", "none", null
    |
    */

    'same_site' => 'lax',

];
