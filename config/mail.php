<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mailer Default (Default Mailer)
    |--------------------------------------------------------------------------
    |
    | Opsi ini mengontrol mailer default yang digunakan untuk mengirim pesan
    | email apa pun yang dikirim oleh aplikasi Anda. Mailer alternatif dapat disiapkan
    | dan digunakan sesuai kebutuhan; namun, mailer ini akan digunakan secara default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Mailer (Mailer Configurations)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mengonfigurasi semua mailer yang digunakan oleh aplikasi Anda plus
    | pengaturan masing-masing. Beberapa contoh telah dikonfigurasi untuk
    | Anda dan Anda bebas menambahkan milik Anda sendiri sesuai kebutuhan aplikasi Anda.
    |
    | Laravel mendukung berbagai driver "transport" email untuk digunakan saat
    | mengirim email. Anda akan menentukan mana yang Anda gunakan untuk
    | mailer Anda di bawah ini. Anda bebas menambahkan mailer tambahan jika diperlukan.
    |
    | Didukung: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array", "failover"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alamat "From" Global (Global "From" Address)
    |--------------------------------------------------------------------------
    |
    | Anda mungkin ingin semua email yang dikirim oleh aplikasi Anda dikirim dari
    | alamat yang sama. Di sini, Anda dapat menentukan nama dan alamat yang
    | digunakan secara global untuk semua email yang dikirim oleh aplikasi Anda.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Email Markdown (Markdown Mail Settings)
    |--------------------------------------------------------------------------
    |
    | Jika Anda menggunakan perenderan email berbasis Markdown, Anda dapat mengonfigurasi
    | tema dan jalur komponen di sini, memungkinkan Anda untuk menyesuaikan desain
    | email. Atau, Anda bisa menggunakan default Laravel saja!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
