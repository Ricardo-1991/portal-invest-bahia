<?php

namespace App\Models;

use App\Support\PublicWatermark;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Event extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'user_id', 'title', 'subtitle', 'description', 'order', 'is_published',
    ];

    public array $translatable = ['title', 'subtitle', 'description'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        // Disco explícito: ver comentário equivalente em App\Models\Listing.
        $this->addMediaCollection('image')->useDisk('public')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('image')
            ->fit(Fit::Contain, 800, 500)
            ->nonQueued();

        PublicWatermark::apply($this->addMediaConversion('public_thumb')
            ->performOnCollections('image')
            ->fit(Fit::Contain, 800, 500))->nonQueued();

        PublicWatermark::apply($this->addMediaConversion('watermarked')
            ->performOnCollections('image'))->nonQueued();
    }

    public function imageUrl(string $conversion = 'watermarked'): ?string
    {
        return $this->getFirstMedia('image')?->getUrl($conversion) ?: null;
    }
}
