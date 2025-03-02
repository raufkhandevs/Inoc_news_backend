<?php

namespace App\Services\News;

use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class NytApiService extends BaseNewsService
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
            'api_identifier' => 'https://www.nytimes.com',
            'base_url' => 'https://api.nytimes.com/svc',
            'logo_url' => 'https://static01.nyt.com/images/misc/NYT_logo_rss_250x40.png'
        ];
    }

    public function fetchArticles(DateTime $fromDate, DateTime $toDate, array $sources = [], array $categories = [], array $authors = []): array
    {
        $params = [
            'api-key' => $this->apiKey,
            'begin_date' => $fromDate->format('Ymd'),
            'end_date' => $toDate->format('Ymd'),
            'sort' => 'newest',
        ];

        // NYT API uses 'fq' (filtered query) parameter for advanced filtering
        $filterQueries = [];

        // Add category filtering
        if (!empty($categories)) {
            $nytSections = $this->mapCategoriesToSections($categories);
            if (!empty($nytSections)) {
                $filterQueries[] = 'section_name:(' . implode(' OR ', $nytSections) . ')';
            }
        }

        // Add author filtering
        if (!empty($authors)) {
            $filterQueries[] = 'byline:(' . implode(' OR ', array_map(fn($author) => "\"$author\"", $authors)) . ')';
        }

        if (!empty($filterQueries)) {
            $params['fq'] = implode(' AND ', $filterQueries);
        }

        try {
            $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', $params);

            if (!$response->successful()) {
                Log::error("NYT API request failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $articles = [];
            $responseData = $response->json()['response']['docs'] ?? [];

            foreach ($responseData as $article) {
                $articleData = $this->transformArticleData($article);
                $savedArticle = $this->saveArticle($articleData);
                if ($savedArticle) {
                    $articles[] = $savedArticle;
                }
            }

            return $articles;

        } catch (\Exception $e) {
            Log::error("Error fetching articles from NYT API: " . $e->getMessage());
            return [];
        }
    }

    protected function transformArticleData(array $rawArticle): array
    {
        // Get the best available image
        $imageUrl = null;
        if (!empty($rawArticle['multimedia'])) {
            $images = array_filter($rawArticle['multimedia'], fn($media) => 
                $media['type'] === 'image' && isset($media['url'])
            );
            if (!empty($images)) {
                $image = reset($images);
                $imageUrl = 'https://www.nytimes.com/' . $image['url'];
            }
        }

        return [
            'title' => $rawArticle['headline']['main'] ?? '',
            'description' => $rawArticle['abstract'] ?? '',
            'content' => $rawArticle['lead_paragraph'] ?? '',
            'url' => $rawArticle['web_url'],
            'image_url' => $imageUrl,
            'published_at' => new DateTime($rawArticle['pub_date']),
            'author_name' => $this->extractAuthor($rawArticle),
            'category' => $this->normalizeCategory($rawArticle['section_name'] ?? '')
        ];
    }

    private function mapCategoriesToSections(array $categories): array
    {
        $sections = [];
        foreach ($categories as $category) {
            if (isset($this->categoryKeywords[strtolower($category)])) {
                $sections = array_merge($sections, $this->categoryKeywords[strtolower($category)]);
            }
        }

        return array_unique($sections);
    }

    private function normalizeCategory(string $nytSection): string
    {
        $nytSection = strtolower($nytSection);
        foreach ($this->categoryKeywords as $normalizedCategory => $sections) {
            if (in_array($nytSection, $sections)) {
                return $normalizedCategory;
            }
        }

        return 'General';
    }

    private function extractAuthor(array $article): string
    {
        if (!empty($article['byline']['original'])) {
            // Remove "By " prefix if present
            return trim(str_replace('By ', '', $article['byline']['original']));
        }
        return 'Unknown';
    }
} 