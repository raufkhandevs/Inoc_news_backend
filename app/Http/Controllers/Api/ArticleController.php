<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\Article;
use App\Http\Resources\Api\ArticleResource;
use App\Models\UserCategoryPreference;
use App\Models\UserAuthorPreference;

/**
 * Controller for handling article-related API endpoints.
 */
class ArticleController extends BaseController
{
    /**
     * Get paginated list of articles with optional search and filters.
     *
     * @param Request $request The HTTP request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->filled('search')) {
            $articlesQuery = Article::search($request->search);
        } else {
            $articlesQuery = Article::query();

            if ($request->filled('category_ids')) {
                $articlesQuery->whereIn('category_id', $request->category_ids);
            }

            if ($request->filled('author_ids')) {
                $articlesQuery->whereIn('author_id', $request->author_ids);
            }
        }

        $articles = $articlesQuery->paginate(Article::PAGE_SIZE);

        return $this->successResponse([
            'articles' => ArticleResource::collection($articles),
            'total' => $articles->total(),
            'page' => $articles->currentPage(),
            'per_page' => $articles->perPage(),
            'last_page' => $articles->lastPage(),
        ]);
    }

    /**
     * Get paginated list of articles based on user's preferred categories and authors.
     *
     * @param Request $request The HTTP request containing authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function myFeeds(Request $request)
    {
        $userId = $request->user()->id;

        $articleQuery = Article::query()
            ->select('articles.*')
            ->distinct()
            ->where(function ($query) use ($userId) {
                // Articles in preferred categories
                $query->whereExists(function ($subQuery) use ($userId) {
                    $subQuery->select(\DB::raw(1))
                        ->from('user_category_preferences')
                        ->whereColumn('articles.category_id', 'user_category_preferences.category_id')
                        ->where('user_category_preferences.user_id', $userId);
                });
                
                // OR articles by preferred authors
                $query->orWhereExists(function ($subQuery) use ($userId) {
                    $subQuery->select(\DB::raw(1))
                        ->from('user_author_preferences')
                        ->whereColumn('articles.author_id', 'user_author_preferences.author_id')
                        ->where('user_author_preferences.user_id', $userId);
                });
            })
            ->latest();

        $articles = $articleQuery->paginate(Article::PAGE_SIZE);

        return $this->successResponse([
            'articles' => ArticleResource::collection($articles),
            'total' => $articles->total(),
            'page' => $articles->currentPage(),
            'per_page' => $articles->perPage(),
            'last_page' => $articles->lastPage(),
        ]);
    }
}
