<?php

namespace App\Contracts;

use DateTime;

/**
 * Interface for news service providers
 */
interface NewsServiceInterface
{
    /**
     * Fetch articles from the news service based on given criteria
     *
     * @param DateTime $fromDate Starting date to fetch articles from
     * @param DateTime $toDate End date to fetch articles to
     * @param array<string> $sources List of news sources to filter by
     * @param array<string> $categories List of article categories to filter by
     * @param array<string> $authors List of authors to filter by
     * @return array<mixed> List of fetched articles
     */
    public function fetchArticles(DateTime $fromDate, DateTime $toDate, array $sources = [], array $categories = [], array $authors = []);
    
    /**
     * Get the name of this news service
     *
     * @return string Name of the news service
     */
    public function getServiceName(): string;
}