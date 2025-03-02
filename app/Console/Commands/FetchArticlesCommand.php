<?php

namespace App\Console\Commands;

use App\Services\ArticleFetcherService;
use Illuminate\Console\Command;
use DateTime;
use App\Models\Category;

class FetchArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch 
        {--hours=1 : Number of hours to look back}
        {--sources= : Optional source IDs to filter by}
        {--categories= : Optional category IDs to filter by}
        {--authors= : Optional author IDs to filter by}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from all configured news sources';

    /**
     * Execute the console command.
     */
    public function handle(ArticleFetcherService $fetcher)
    {
        $toDate = new DateTime();
        $fromDate = (clone $toDate)->modify('-' . $this->option('hours') . ' hours');

        $this->info("Fetching articles from {$fromDate->format('Y-m-d H:i:s')} to {$toDate->format('Y-m-d H:i:s')}");

        $categories = Category::all()->pluck('name')->toArray(); // TODO: need to optimize this, this can be a bottleneck

        // Fetch articles from all configured news sources
        $articles = $fetcher->fetchAllSources(
            $fromDate,
            $toDate,
            $this->option('sources') ?? [],
            $this->option('categories') ?? $categories,
            $this->option('authors') ?? []
        );

        $this->info('Article fetch completed. Total articles fetched: ' . count($articles));
    }
}
