<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'source_id',
    ];  

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
