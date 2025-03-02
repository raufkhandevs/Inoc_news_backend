<?php

namespace App\Services\News;

use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;

class NewsApiService extends BaseNewsService
{
    private string $apiKey;
    
    /**
     * Category to keyword mapping for search queries
     */
    private array $categoryKeywords = [];

    public function __construct()
    {
        parent::__construct();
        $this->apiKey = $this->getServiceConfig()['key'];

        $this->categoryKeywords = Category::DEFAULT_CATEGORIES_WITH_TAGS;
    }

    public function getServiceName(): string
    {
        return $this->getServiceConfig()['name'];
    }

    protected function getSourceConfig(): array
    {
        return [
            'api_identifier' => 'https://newsapi.org',
            'base_url' => 'https://newsapi.org/v2',
            'logo_url' => 'https://newsapi.org/images/n-logo-border.png'
        ];
    }

    public function fetchArticles(DateTime $fromDate, DateTime $toDate, array $sources = [], array $categories = [], array $authors = []): array
    {
        $query = $this->buildSearchQuery($categories, $authors);
        
        $params = [
            'apiKey' => $this->apiKey,
            'from' => $this->formatDate($fromDate),
            'to' => $this->formatDate($toDate),
            'language' => 'en',
            'sortBy' => 'publishedAt',
            'pageSize' => $this->getConfiguredPageSize()
        ];

        // Add query if we have one
        if (!empty($query)) {
            $params['q'] = $query;
        }

        // Add source filtering if specified
        if (!empty($sources)) {
            $params['sources'] = implode(',', $sources);
        }

        try {
            Log::info('Fetching articles from NewsAPI', [
                'query' => $query,
                'from' => $fromDate->format('Y-m-d H:i:s'),
                'to' => $toDate->format('Y-m-d H:i:s')
            ]);

            $response = Http::get('https://newsapi.org/v2/everything', $params);

            if (!$response->successful()) {
                Log::error("NewsAPI request failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $articles = [];
            $responseData = $response->json()['articles'] ?? [];

            foreach ($responseData as $article) {
                $articleData = $this->transformArticleData($article);
                $savedArticle = $this->saveArticle($articleData);
                if ($savedArticle) {
                    $articles[] = $savedArticle;
                }
            }

            Log::info('Successfully fetched articles from NewsAPI', [
                'count' => count($articles)
            ]);

            return $articles;

        } catch (\Exception $e) {
            Log::error("Error fetching articles from NewsAPI: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return [];
        }
    }

    protected function transformArticleData(array $rawArticle): array
    {
        return [
            'title' => $rawArticle['title'],
            'description' => $rawArticle['description'] ?? '',
            'content' => $rawArticle['content'] ?? '',
            'url' => $rawArticle['url'],
            'image_url' => $rawArticle['urlToImage'] ?? null,
            'published_at' => new \DateTime($rawArticle['publishedAt']),
            'author_name' => $rawArticle['author'] ?? 'Unknown',
            'category' => $this->determineCategory($rawArticle['title'], $rawArticle['description'] ?? '')
        ];
    }

    /**
     * Build the search query based on categories and authors
     */
    private function buildSearchQuery(array $categories = [], array $authors = []): string
    {
        $queryParts = [];

        // Add category-based keywords
        if (!empty($categories)) {
            $categoryQueries = [];
            foreach ($categories as $category) {
                if (isset($this->categoryKeywords[$category])) {
                    $categoryQueries[] = '(' . implode(' OR ', $this->categoryKeywords[$category]) . ')';
                } else {
                    // For custom categories, use the category name as a keyword
                    $categoryQueries[] = '"' . $category . '"';
                }
            }
            if (!empty($categoryQueries)) {
                $queryParts[] = '(' . implode(' OR ', $categoryQueries) . ')';
            }
        }

        // Add author filtering
        if (!empty($authors)) {
            $authorQueries = array_map(function($author) {
                // Escape quotes in author names and wrap in quotes for exact match
                $author = str_replace('"', '\"', $author);
                return 'author:"' . $author . '"';
            }, $authors);
            $queryParts[] = '(' . implode(' OR ', $authorQueries) . ')';
        }

        // If no specific filters, return empty string to get all articles
        if (empty($queryParts)) {
            return '';
        }

        // Combine all parts with AND
        return implode(' AND ', $queryParts);
    }

    /**
     * Determine the category based on article content
     */
    private function determineCategory(string $title, string $description): string
    {
        $content = strtolower($title . ' ' . $description);
        
        // Check each category's keywords
        foreach ($this->categoryKeywords as $category => $tags) {
            $keywords = '(' . implode(' OR ', $tags) . ')';
            // Extract individual keywords from the query
            preg_match_all('/\w+/', $keywords, $matches);
            $keywordList = $matches[0];
            
            foreach ($keywordList as $keyword) {
                if (str_contains($content, strtolower($keyword))) {
                    return $category;
                }
            }
        }

        return 'general';
    }
} 