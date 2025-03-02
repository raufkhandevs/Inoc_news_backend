<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ArticleFetcherService;
use App\Services\News\NewsApiService;
use App\Services\News\NytApiService;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the ArticleFetcherService with configured services
        $this->app->singleton(ArticleFetcherService::class, function ($app) {
            $enabledServices = [];
            
            // Add NewsAPI service if enabled
            if (config('news.services.newsapi.enabled', false)) {
                $enabledServices[] = $app->make(NewsApiService::class);
            }

            // Add New York Times service if enabled
            if (config('news.services.nyt.enabled', false)) {
                $enabledServices[] = $app->make(NytApiService::class);
            }

            // Add other services here

            return new ArticleFetcherService($enabledServices);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
