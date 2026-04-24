<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Koneksi Database Default (Default Database Connection Name)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan mana dari koneksi database di bawah ini yang ingin
    | Anda gunakan sebagai koneksi default untuk semua pekerjaan database. Tentu saja
    | Anda dapat menggunakan banyak koneksi sekaligus menggunakan pustaka Database.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Koneksi Database (Database Connections)
    |--------------------------------------------------------------------------
    |
    | Berikut adalah masing-masing konfigurasi koneksi database untuk aplikasi Anda.
    | Tentu saja, contoh konfigurasi setiap platform database yang
    | didukung oleh Laravel ditunjukkan di bawah ini untuk memudahkan pengembangan.
    |
    |
    | Semua pekerjaan database di Laravel dilakukan melalui fasilitas PHP PDO
    | jadi pastikan Anda memiliki driver untuk database pilihan Anda
    | terinstal di mesin Anda sebelum Anda memulai pengembangan.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Tabel Repositori Migrasi (Migration Repository Table)
    |--------------------------------------------------------------------------
    |
    | Tabel ini melacak semua migrasi yang telah dijalankan untuk
    | aplikasi Anda. Menggunakan informasi ini, kami dapat menentukan migrasi
    | mana di disk yang sebenarnya belum dijalankan di database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Database Redis (Redis Databases)
    |--------------------------------------------------------------------------
    |
    | Redis adalah penyimpanan kunci-nilai (key-value) sumber terbuka, cepat, dan canggih yang juga
    | menyediakan kumpulan perintah yang lebih kaya daripada sistem kunci-nilai tipikal
    | seperti APC atau Memcached. Laravel memudahkan untuk langsung menggunakannya.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
