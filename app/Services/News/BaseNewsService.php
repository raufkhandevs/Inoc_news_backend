<?php

namespace App\Services\News;

use App\Contracts\NewsServiceInterface;
use App\Models\Article;
use App\Models\Source;
use App\Models\Category;
use App\Models\Author;
use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseNewsService implements NewsServiceInterface
{
    protected Source $source;
    
    public function __construct()
    {
        $this->source = Source::firstOrCreate(
            ['name' => $this->getServiceName()],
            $this->getSourceConfig()
        );
    }

    /**
     * Get source specific configuration
     *
     * @return array
     */
    abstract protected function getSourceConfig(): array;

    /**
     * Transform raw article data into standardized format
     *
     * @param array $rawArticle
     * @return array
     */
    abstract protected function transformArticleData(array $rawArticle): array;

    protected function saveArticle(array $articleData): ?Article
    {
        try {
            // Check for duplicate using URL or title + published date
            $existingArticle = Article::where(function($query) use ($articleData) {
                $query->where('url', $articleData['url'])
                    ->orWhere(function($q) use ($articleData) {
                        $q->where('title', $articleData['title'])
                          ->where('published_at', $articleData['published_at']);
                    });
            })->first();

            if ($existingArticle) {
                Log::info("Duplicate article found: " . $articleData['title']);
                return null;
            }

            // Find or create author
            $author = Author::firstOrCreate(
                ['name' => $articleData['author_name']],
                [
                    'slug' => Str::slug($articleData['author_name']),
                    'source_id' => $this->source->id
                ]
            );

            // Find or create category
            $category = Category::firstOrCreate(
                ['name' => $articleData['category']],
                ['slug' => Str::slug($articleData['category'])]
            );

            // Create the article
            $article = Article::create([
                'source_id' => $this->source->id,
                'author_id' => $author->id,
                'category_id' => $category->id,
                'title' => $articleData['title'],
                'slug' => Str::slug($articleData['title']),
                'description' => $articleData['description'],
                'content' => $articleData['content'],
                'url' => $articleData['url'],
                'image_url' => $articleData['image_url'] ?? null,
                'published_at' => $articleData['published_at'],
            ]);

            Log::info("Article saved successfully: " . $article->title);
            return $article;

        } catch (\Exception $e) {
            Log::error("Error saving article: " . $e->getMessage(), [
                'source' => $this->getServiceName(),
                'article_data' => $articleData
            ]);
            return null;
        }
    }

    /**
     * Apply filters to the API request parameters
     *
     * @param array $params
     * @param array $sources
     * @param array $categories
     * @param array $authors
     * @return array
     */
    protected function applyFilters(array $params, array $sources, array $categories, array $authors): array
    {
        if (!empty($sources)) {
            $params['sources'] = implode(',', $sources);
        }
        
        if (!empty($categories)) {
            $params['categories'] = implode(',', $categories);
        }
        
        if (!empty($authors)) {
            $params['authors'] = implode(',', $authors);
        }
        
        return $params;
    }

    protected function formatDate(DateTime $date): string
    {
        return $date->format('Y-m-d\TH:i:s\Z');
    }

    protected function getServiceConfig(): array
    {
        $serviceKey = $this->getServiceKey();
        return config("news.services.{$serviceKey}");
    }

    /**
     * Get the service key from configuration
     *
     * @return string
     */
    protected function getServiceKey(): string
    {
        $configServices = config('news.services');
        foreach ($configServices as $key => $config) {
            if ($config['class'] === get_class($this)) {
                return $key;
            }
        }
        throw new \RuntimeException('Service not found in configuration');
    }

    /**
     * Get the configured page size for the service
     *
     * @return int
     */
    protected function getConfiguredPageSize(): int
    {
        return $this->getServiceConfig()['page_size'] ?? 50;
    }
} 