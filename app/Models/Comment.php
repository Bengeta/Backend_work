<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->BelongsTo(Post::class, 'post_id');
    }

    public function scopeOrdered(Builder $builder): Builder
    {
        return $builder
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public static function ordered(Collection $collection): Collection
    {
        return $collection
            ->sortByDesc('created_at')
            ->sortByDesc('id');
    }
}
