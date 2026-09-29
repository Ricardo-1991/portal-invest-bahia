<?php

namespace App\Models;

use App\Support\AreaUnits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Listing extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected static function booted(): void
    {
        // Slugs em branco são gerados automaticamente. Um slug preenchido pelo
        // usuário nunca deve ser alterado silenciosamente para outra URL.
        static::saving(function (Listing $listing): void {
            $listing->currency ??= 'BRL';
            $listing->area_unit ??= 'ha';
            $listing->area_sqm = AreaUnits::toSquareMeters($listing->area, $listing->area_unit);

            $source = $listing->slug;

            if (blank($source)) {
                $titles = $listing->getTranslations('title');
                $firstTitle = $titles['pt'] ?? collect($titles)->first(fn ($value) => filled($value));
                $listing->slug = static::generateUniqueSlug($firstTitle ?: 'anuncio', $listing->getKey());

                return;
            }

            $slug = Str::slug($source) ?: 'anuncio';

            if (static::slugExists($slug, $listing->getKey())) {
                throw ValidationException::withMessages([
                    'data.slug' => 'Já existe um classificado com este slug. Escolha outro.',
                ]);
            }

            $listing->slug = $slug;
        });
    }

    public static function generateUniqueSlug(string $source, int|string|null $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'anuncio';
        $slug = $base;
        $suffix = 1;

        while (static::slugExists($slug, $ignoreId)) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private static function slugExists(string $slug, int|string|null $ignoreId = null): bool
    {
        return static::query()
            ->where('slug', $slug)
            ->when(filled($ignoreId), fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }

    /** Categorias válidas. */
    public const CATEGORIES = ['fazenda', 'ativo', 'servico', 'apartamento', 'casa', 'sitio'];

    /** Situações válidas. */
    public const STATUSES = ['draft', 'published', 'hidden'];

    /** Idiomas suportados no conteúdo. */
    public const LOCALES = ['pt', 'en', 'es', 'it'];

    protected $fillable = [
        'user_id', 'category', 'status', 'slug', 'region', 'latitude', 'longitude', 'price', 'currency', 'area', 'area_unit',
        'title', 'subtitle', 'description',
    ];

    /** Atributos traduzíveis (spatie/laravel-translatable). */
    public array $translatable = ['title', 'subtitle', 'description'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'area' => 'decimal:2',
            'area_sqm' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
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
        // Disco explícito: sem isso o Filament recai em `filament.default_filesystem_disk`
        // (= FILESYSTEM_DISK = 'local', privado) e as imagens ficam inacessíveis publicamente.
        $this->addMediaCollection('main')->useDisk('public')->singleFile();
        $this->addMediaCollection('gallery')->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 600, 400)
            ->nonQueued();
    }

    /** URL da imagem principal (ou null). */
    public function mainImageUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('main') ?? $this->getFirstMedia('gallery');

        return $media?->getUrl($conversion) ?: null;
    }
}
