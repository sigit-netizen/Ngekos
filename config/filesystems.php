<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disk Sistem Berkas Default (Default Filesystem Disk)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan disk sistem berkas default yang harus digunakan
    | oleh framework. Disk "local", serta berbagai disk berbasis cloud
    | tersedia untuk aplikasi Anda. Silakan simpan!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Disk Sistem Berkas (Filesystem Disks)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mengonfigurasi "disk" sistem berkas sebanyak yang Anda inginkan, dan Anda
    | bahkan dapat mengonfigurasi banyak disk dengan driver yang sama. Nilai default telah
    | disiapkan untuk setiap driver sebagai contoh dari nilai yang diperlukan.
    |
    | Driver yang Didukung: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Tautan Simbolik (Symbolic Links)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mengonfigurasi tautan simbolik yang akan dibuat saat perintah
    | Artisan `storage:link` dijalankan. Kunci array harus berupa lokasi tautan
    | dan nilainya harus berupa targetnya.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
