<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferenceCategory extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(Reference::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
