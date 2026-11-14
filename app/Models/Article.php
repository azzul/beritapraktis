<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'image_alt',
        'image_caption',
        'word_count',
        'outbound_links_count',
        'outbound_links',
        'status',
        'rejection_reason',
        'views_count',
        'published_at',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'outbound_links' => 'array',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'word_count' => 'integer',
            'outbound_links_count' => 'integer',
            'views_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function moderationLogs(): HasMany
    {
        return $this->hasMany(ModerationLog::class)->latest();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending_review');
    }

    public function getImageUrlAttribute(): string
    {
        if (str_starts_with($this->featured_image, 'http')) {
            return $this->featured_image;
        }

        if (str_starts_with($this->featured_image, 'images/')) {
            return asset($this->featured_image);
        }

        $relative = ltrim($this->featured_image, '/');
        if (is_file(public_path('storage/' . $relative))) {
            return asset('storage/' . $relative);
        }

        return url('/media/' . $relative);
    }

    public function getReadingTimeMinutesAttribute(): int
    {
        return max(1, (int) ceil($this->word_count / 200));
    }
}
