<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    const DEFAULT_CATEGORIES_WITH_TAGS = [
        'technology' => ['tech', 'technology', 'software', 'ai', 'artificial intelligence', 'digital'],
        'business' => ['business', 'economy', 'market', 'finance', 'stocks'],
        'sports' => ['sports', 'football', 'basketball', 'soccer', 'tennis'],
        'entertainment' => ['entertainment', 'movie', 'music', 'celebrity', 'pop culture'],
        'health' => ['health', 'medical', 'healthcare', 'wellness'],
        'science' => ['science', 'research', 'discovery', 'space', 'climate'],
        'politics' => ['politics', 'government', 'election', 'policy'],
        'general' => ['general'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
