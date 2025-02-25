<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    const NEWS_API = 'news-api';

    const API_TYPES = [
        self::NEWS_API,
    ];

    protected $fillable = [
        'name',
        'api_identifier',
        'api_type',
        'base_url',
        'api_key',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function authors(): HasMany
    {
        return $this->hasMany(Author::class);
    }
}
