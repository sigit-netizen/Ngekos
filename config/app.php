<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Aplikasi (Application Name)
    |--------------------------------------------------------------------------
    |
    | Nilai ini adalah nama aplikasi Anda. Nilai ini digunakan ketika
    | framework perlu menempatkan nama aplikasi dalam notifikasi atau
    | lokasi lain yang diperlukan oleh aplikasi atau paket-paketnya.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Lingkungan Aplikasi (Application Environment)
    |--------------------------------------------------------------------------
    |
    | Nilai ini menentukan "lingkungan" tempat aplikasi Anda saat ini
    | berjalan. Hal ini dapat menentukan bagaimana Anda memilih untuk mengonfigurasi
    | berbagai layanan yang digunakan aplikasi. Atur ini di file ".env" Anda.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mode Debug Aplikasi (Application Debug Mode)
    |--------------------------------------------------------------------------
    |
    | Saat aplikasi Anda dalam mode debug, pesan kesalahan terperinci dengan
    | stack trace akan ditampilkan pada setiap kesalahan yang terjadi di dalam
    | aplikasi Anda. Jika dinonaktifkan, halaman kesalahan generik sederhana akan ditampilkan.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL Aplikasi (Application URL)
    |--------------------------------------------------------------------------
    |
    | URL ini digunakan oleh konsol untuk menghasilkan URL dengan benar saat menggunakan
    | alat baris perintah Artisan. Anda harus menyetel ini ke akar (root) dari
    | aplikasi Anda sehingga digunakan saat menjalankan tugas-tugas Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),

    'asset_url' => env('ASSET_URL', '/'),

    /*
    |--------------------------------------------------------------------------
    | Zona Waktu Aplikasi (Application Timezone)
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan zona waktu default untuk aplikasi Anda, yang
    | akan digunakan oleh fungsi tanggal PHP. Kami telah menetapkannya
    | ke default yang masuk akal untuk Anda secara langsung.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Lokalan Aplikasi (Application Locale Configuration)
    |--------------------------------------------------------------------------
    |
    | Lokalan aplikasi menentukan lokalan default yang akan digunakan
    | oleh penyedia layanan terjemahan. Anda bebas menyetel nilai ini
    | ke lokalan mana pun yang akan didukung oleh aplikasi.
    |
    */

    'locale' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Lokalan Cadangan Aplikasi (Application Fallback Locale)
    |--------------------------------------------------------------------------
    |
    | Lokalan cadangan menentukan lokalan yang akan digunakan ketika lokalan saat ini
    | tidak tersedia. Anda dapat mengubah nilainya sesuai dengan salah satu
    | folder bahasa yang disediakan melalui aplikasi Anda.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Lokalan Faker (Faker Locale)
    |--------------------------------------------------------------------------
    |
    | Lokalan ini akan digunakan oleh pustaka PHP Faker saat menghasilkan data palsu
    | untuk seed database Anda. Misalnya, ini akan digunakan untuk mendapatkan
    | nomor telepon lokal, informasi alamat jalan, dan lainnya.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Kunci Enkripsi (Encryption Key)
    |--------------------------------------------------------------------------
    |
    | Kunci ini digunakan oleh layanan enkripsi Illuminate dan harus disetel
    | ke string acak 32 karakter, jika tidak, string yang dienkripsi ini
    | tidak akan aman. Harap lakukan ini sebelum menerapkan aplikasi!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Driver Mode Pemeliharaan (Maintenance Mode Driver)
    |--------------------------------------------------------------------------
    |
    | Opsi konfigurasi ini menentukan driver yang digunakan untuk menentukan dan
    | mengelola status "mode pemeliharaan" Laravel. Driver "cache" akan
    | memungkinkan mode pemeliharaan dikontrol di beberapa mesin.
    |
    | Driver yang didukung: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Provider yang Dimuat Otomatis (Autoloaded Service Providers)
    |--------------------------------------------------------------------------
    |
    | Penyedia layanan (Service Providers) yang tercantum di sini akan dimuat secara otomatis pada
    | permintaan ke aplikasi Anda. Jangan ragu untuk menambahkan layanan Anda sendiri ke
    | array ini untuk memberikan fungsionalitas tambahan ke aplikasi Anda.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        NotificationChannels\WebPush\WebPushServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Alias Kelas (Class Aliases)
    |--------------------------------------------------------------------------
    |
    | Array alias kelas ini akan didaftarkan saat aplikasi ini
    | dimulai. Namun, jangan ragu untuk mendaftarkan sebanyak yang Anda inginkan karena
    | alias dimuat secara "lazy" sehingga tidak menghambat performa.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),

];
