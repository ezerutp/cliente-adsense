<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'description',
    'iframe_src',
    'thumbnail_url',
    'sort_order',
    'is_active',
    'published_at',
])]
class VideoPost extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(fn (Builder $query) => $query
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now()));
    }
}
