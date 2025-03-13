<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    protected $fillable = [
        'name',
        'api_identifier',
        'api_type',
        'base_url',
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
