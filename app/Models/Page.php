<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'hero_heading', 'hero_subheading', 'hero_image',
        'body', 'meta_title', 'meta_description', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
