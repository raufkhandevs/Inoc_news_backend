<?php

namespace App\Services\News;

use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class GuardianApiService extends BaseNewsService
{
    private string $apiKey;

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
            'api_identifier' => 'https://www.theguardian.com',
            'base_url' => 'https://content.guardianapis.com',
            'logo_url' => 'https://assets.guim.co.uk/images/guardian-logo-rss.c45beb1bafa34b347ac333af2e6fe23f.png'
        ];
    }

    public function fetchArticles(DateTime $fromDate, DateTime $toDate, array $sources = [], array $categories = [], array $authors = []): array
    {
        $params = [
            'api-key' => $this->apiKey,
            'from-date' => $fromDate->format('Y-m-d'),
            'to-date' => $toDate->format('Y-m-d'),
            'show-fields' => 'all',
            'page-size' => 50,
            'order-by' => 'newest'
        ];

        // Map our categories to Guardian sections if provided
        if (!empty($categories)) {
            $params['section'] = $this->mapCategoriesToSections($categories);
        }

        // Guardian API supports tag-based filtering for authors
        if (!empty($authors)) {
            $params['tag'] = implode('|', array_map(fn($author) => "profile/$author", $authors));
        }

        try {
            $response = Http::get('https://content.guardianapis.com/search', $params);

            if (!$response->successful()) {
                Log::error("Guardian API request failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $articles = [];
            $responseData = $response->json()['response']['results'] ?? [];

            foreach ($responseData as $article) {
                $articleData = $this->transformArticleData($article);
                $savedArticle = $this->saveArticle($articleData);
                if ($savedArticle) {
                    $articles[] = $savedArticle;
                }
            }

            return $articles;

        } catch (\Exception $e) {
            Log::error("Error fetching articles from Guardian API: " . $e->getMessage());
            return [];
        }
    }

    protected function transformArticleData(array $rawArticle): array
    {
        $fields = $rawArticle['fields'] ?? [];
        
        return [
            'title' => $rawArticle['webTitle'],
            'description' => $fields['trailText'] ?? '',
            'content' => $fields['bodyText'] ?? '',
            'url' => $rawArticle['webUrl'],
            'image_url' => $fields['thumbnail'] ?? null,
            'published_at' => new DateTime($rawArticle['webPublicationDate']),
            'author_name' => $fields['byline'] ?? 'Unknown',
            'category' => $this->normalizeCategory($rawArticle['sectionName'])
        ];
    }

    private function mapCategoriesToSections(array $categories): string
    {
        $sectionMap = array_map(fn($tags) => implode('|', $tags), $this->categoryKeywords);

        $sections = array_map(function($category) use ($sectionMap) {
            return $sectionMap[strtolower($category)] ?? null;
        }, $categories);

        return implode('|', array_filter($sections));
    }

    private function normalizeCategory(string $guardianSection): string
    {
        foreach ($this->categoryKeywords as $normalizedCategory => $sections) {
            if (in_array(strtolower($guardianSection), $sections)) {
                return $normalizedCategory;
            }
        }

        return 'General';
    }
} 