<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => '/',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'key_file' => env('GOOGLE_APPLICATION_CREDENTIALS'), // Pastikan ini ada dan path-nya benar!
            'storage_api_uri' => null, // Gunakan default Google Cloud Storage API URI
            'visibility' => 'public', // Agar file bisa diakses via URL
        ],

        // 's3' => [
        //     'driver' => 's3',
        //     'key' => env('DO_SPACES_KEY'),
        //     'secret' => env('DO_SPACES_SECRET'),
        //     'region' => env('DO_SPACES_REGION'),
        //     'bucket' => env('DO_SPACES_BUCKET'),
        //     'endpoint' => env('DO_SPACES_ENDPOINT'),
        //     'use_path_style_endpoint' => false,
        //     'visibility' => 'public', // Agar file bisa diakses via URL
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
