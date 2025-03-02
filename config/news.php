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
    ],
];
