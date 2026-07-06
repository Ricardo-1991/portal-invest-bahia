<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Listing extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    /** Categorias válidas. */
    public const CATEGORIES = ['fazenda', 'ativo', 'servico'];

    /** Situações válidas. */
    public const STATUSES = ['draft', 'published', 'hidden'];

    /** Idiomas suportados no conteúdo. */
    public const LOCALES = ['pt', 'en', 'es', 'it'];

    protected $fillable = [
        'user_id', 'category', 'status', 'slug', 'region', 'price',
        'title', 'subtitle', 'description',
    ];

    /** Atributos traduzíveis (spatie/laravel-translatable). */
    public array $translatable = ['title', 'subtitle', 'description'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Apenas anúncios públicos. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 600, 400)
            ->nonQueued();
    }

    /** URL da imagem principal (ou null). */
    public function mainImageUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('main') ?? $this->getFirstMedia('gallery');

        return $media?->getUrl($conversion) ?: null;
    }
}
