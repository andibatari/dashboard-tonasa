<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => null, // Sanctum handle otomatis
        ],
        
        // Guard untuk Admin
        'admin_users' => [
            'driver' => 'session',
            'provider' => 'admin_users', // Gunakan provider 'admin_users'
        ],
    ],

    'providers' => [
        'admin_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],
        
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reset Kata Sandi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan "password broker" yang digunakan untuk
    | mengirim tautan reset password. Setiap broker memiliki driver yang
    | unik dan model yang sesuai.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        
        'admin_users' => [
            'provider' => 'admin_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Opsi Otoritas Reset Kata Sandi
    |--------------------------------------------------------------------------
    |
    | Opsi ini mengontrol durasi token reset password yang valid
    | dan berapa kali pengguna dapat membuat token dalam satu jam.
    |
    */

    'password_timeout' => 10800,
];

