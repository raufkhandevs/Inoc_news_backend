<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    const DEFAULT_CATEGORIES = [
        'technology',
        'business',
        'sports',
        'entertainment',
        'health',
        'science',
        'politics',
        'general',
    ];

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
