<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Autentikasi (Authentication Defaults)
    |--------------------------------------------------------------------------
    |
    | Opsi ini mengontrol "guard" autentikasi default dan opsi reset kata sandi
    | untuk aplikasi Anda. Anda dapat mengubah default ini sesuai kebutuhan,
    | namun ini adalah awal yang sempurna untuk sebagian besar aplikasi.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Guard Autentikasi (Authentication Guards)
    |--------------------------------------------------------------------------
    |
    | Selanjutnya, Anda dapat mendefinisikan setiap guard autentikasi untuk aplikasi Anda.
    | Tentu saja, konfigurasi default yang bagus telah ditentukan untuk Anda
    | di sini yang menggunakan penyimpanan sesi dan penyedia pengguna Eloquent.
    |
    | Semua driver autentikasi memiliki penyedia pengguna. Ini menentukan bagaimana
    | pengguna benar-benar diambil dari database Anda atau mekanisme penyimpanan
    | lain yang digunakan oleh aplikasi ini untuk menyimpan data pengguna Anda.
    |
    | Didukung: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Penyedia Pengguna (User Providers)
    |--------------------------------------------------------------------------
    |
    | Semua driver autentikasi memiliki penyedia pengguna. Ini menentukan bagaimana
    | pengguna benar-benar diambil dari database Anda atau mekanisme penyimpanan
    | lain yang digunakan oleh aplikasi ini untuk menyimpan data pengguna Anda.
    |
    | Jika Anda memiliki beberapa tabel atau model pengguna, Anda dapat mengonfigurasi
    | beberapa sumber yang mewakili setiap model / tabel. Sumber-sumber ini kemudian
    | dapat ditetapkan ke guard autentikasi tambahan yang telah Anda tetapkan.
    |
    | Didukung: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mereset Kata Sandi (Resetting Passwords)
    |--------------------------------------------------------------------------
    |
    | Anda dapat menentukan beberapa konfigurasi reset kata sandi jika Anda memiliki
    | lebih dari satu tabel atau model pengguna dalam aplikasi dan Anda ingin memiliki
    | pengaturan reset kata sandi yang terpisah berdasarkan tipe pengguna tertentu.
    |
    | Waktu kedaluwarsa adalah jumlah menit setiap token reset akan dianggap valid.
    | Fitur keamanan ini menjaga token agar berumur pendek sehingga mereka memiliki
    | lebih sedikit waktu untuk ditebak. Anda dapat mengubah ini sesuai kebutuhan.
    |
    | Pengaturan throttle adalah jumlah detik yang harus ditunggu pengguna sebelum
    | menghasilkan lebih banyak token reset kata sandi. Ini mencegah pengguna dari
    | menghasilkan jumlah token reset kata sandi yang sangat besar dengan cepat.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Waktu Habis Konfirmasi Kata Sandi (Password Confirmation Timeout)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan jumlah detik sebelum konfirmasi kata sandi
    | habis waktu dan pengguna diminta untuk memasukkan kembali kata sandi mereka melalui
    | layar konfirmasi. Secara default, waktu habis berlangsung selama tiga jam.
    |
    */

    'password_timeout' => 10800,

];
