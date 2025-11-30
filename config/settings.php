<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | The settings classes that should be loaded.
    |
    */
    'settings' => [
        App\Settings\CommunicationSettings::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | The settings are cached to improve performance. Here you can configure
    | the cache store and key.
    |
    */
    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', false),
        'store' => null,
        'prefix' => null,
        'ttl' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository
    |--------------------------------------------------------------------------
    |
    | The repository where the settings are stored.
    |
    */
    'repository' => Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository::class,

    /*
    |--------------------------------------------------------------------------
    | Encoder
    |--------------------------------------------------------------------------
    |
    | The encoder used to encode and decode the settings.
    |
    */
    'encoder' => null,
];
