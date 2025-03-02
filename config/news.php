<?php

return [
    /*
    |--------------------------------------------------------------------------
    | News Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for all news services.
    | Enable or disable services and configure their specific settings.
    |
    */
    'services' => [
        'newsapi' => [
            'enabled' => env('NEWSAPI_ENABLED', true),
            'name' => 'NewsAPI.org',
            'class' => \App\Services\News\NewsApiService::class,
            'page_size' => env('NEWSAPI_PAGE_SIZE', 50),
            'key' => env('NEWSAPI_KEY'),
        ],

        'nyt' => [
            'enabled' => env('NYT_ENABLED', true),
            'name' => 'New York Times',
            'class' => \App\Services\News\NytApiService::class,
            'page_size' => env('NYT_PAGE_SIZE', 50),
            'key' => env('NYT_KEY'),
        ],

        'guardian' => [
            'enabled' => env('GUARDIAN_ENABLED', true),
            'name' => 'The Guardian',
            'class' => \App\Services\News\GuardianApiService::class,
            'page_size' => env('GUARDIAN_PAGE_SIZE', 50),
            'key' => env('GUARDIAN_KEY'),
        ],
    ],
];
