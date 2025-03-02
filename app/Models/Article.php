<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use Searchable;

    /**
     * The number of articles to return per page.
     */
    const PAGE_SIZE = 10;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'source_id',
        'category_id',
        'author_id',
        'title',
        'slug',
        'description',
        'content',
        'url',
        'image_url',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the source associated with the article.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the category associated with the article.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author associated with the article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
        ];
    }
}
