<?php

namespace App\Services;

use App\Contracts\NewsServiceInterface;
use DateTime;
use Illuminate\Support\Facades\Log;

/**
 * Service for fetching articles from multiple news sources
 */
class ArticleFetcherService
{
    /**
     * Array of news service instances implementing NewsServiceInterface
     *
     * @var array<NewsServiceInterface>
     */
    private array $newsServices;

    /**
     * Create a new ArticleFetcherService instance
     *
     * @param array<NewsServiceInterface> $newsServices Array of news service instances
     */
    public function __construct(array $newsServices)
    {
        $this->newsServices = $newsServices;
    }

    /**
     * Fetch articles from all configured news sources
     *
     * @param DateTime $fromDate Starting date to fetch articles from
     * @param DateTime $toDate End date to fetch articles to
     * @param array<string> $sources List of news sources to filter by
     * @param array<string> $categories List of article categories to filter by
     * @param array<string> $authors List of authors to filter by
     * @return array<mixed> Combined list of articles from all sources
     */
    public function fetchAllSources(
        DateTime $fromDate,
        DateTime $toDate,
        array $sources = [],
        array $categories = [],
        array $authors = []
    ): array {

        $allArticles = [];

        foreach ($this->newsServices as $service) {
            try {
                Log::info("Starting article fetch for " . $service->getServiceName(), [
                    'from' => $fromDate->format('Y-m-d H:i:s'),
                    'to' => $toDate->format('Y-m-d H:i:s')
                ]);

                $articles = $service->fetchArticles($fromDate, $toDate, $sources, $categories, $authors);
                $allArticles = array_merge($allArticles, $articles);

                Log::info("Completed fetch for " . $service->getServiceName(), [
                    'articles_found' => count($articles)
                ]);
            } catch (\Exception $e) {
                Log::error("Error fetching articles from " . $service->getServiceName() . ": " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        }

        return $allArticles;
    }
}