<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Listing extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected static function booted(): void
    {
        // Garante um slug único a partir do primeiro título preenchido (o
        // corretor pode ter publicado em qualquer um dos 4 idiomas).
        static::saving(function (Listing $listing): void {
            if (blank($listing->slug)) {
                $titles = $listing->getTranslations('title');
                $firstTitle = $titles['pt'] ?? collect($titles)->first(fn ($value) => filled($value));
                $base = Str::slug($firstTitle ?: 'anuncio');
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->whereKeyNot($listing->getKey())->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $listing->slug = $slug;
            }
        });
    }

    /** Categorias válidas. */
    public const CATEGORIES = ['fazenda', 'ativo', 'servico'];

    /** Situações válidas. */
    public const STATUSES = ['draft', 'published', 'hidden'];

    /** Idiomas suportados no conteúdo. */
    public const LOCALES = ['pt', 'en', 'es', 'it'];

    protected $fillable = [
        'user_id', 'category', 'status', 'slug', 'region', 'price', 'area',
        'title', 'subtitle', 'description',
    ];

    /** Atributos traduzíveis (spatie/laravel-translatable). */
    public array $translatable = ['title', 'subtitle', 'description'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'area' => 'decimal:2',
        ];
    }

    /** Preço por hectare, quando preço e área estiverem preenchidos. */
    public function pricePerHectare(): ?float
    {
        if ((float) $this->price > 0 && (float) $this->area > 0) {
            return (float) $this->price / (float) $this->area;
        }

        return null;
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
        // Disco explícito: sem isso o Filament recai em `filament.default_filesystem_disk`
        // (= FILESYSTEM_DISK = 'local', privado) e as imagens ficam inacessíveis publicamente.
        $this->addMediaCollection('main')->useDisk('public')->singleFile();
        $this->addMediaCollection('gallery')->useDisk('public');
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
