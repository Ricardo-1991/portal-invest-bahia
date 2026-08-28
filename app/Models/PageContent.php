<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class PageContent extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    /** Chaves de página válidas. */
    public const KEYS = ['home', 'fazenda', 'ativo', 'servico', 'informacoes', 'contatos'];

    protected $fillable = ['key', 'title', 'body'];

    public array $translatable = ['title', 'body'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero_video')
            ->useDisk('public')
            ->singleFile();
    }

    /** URL pública do vídeo administrável da página inicial. */
    public function heroVideoUrl(): ?string
    {
        return $this->getFirstMedia('hero_video')?->getUrl();
    }

    /** Retorna (ou cria) o conteúdo de uma página pela chave. */
    public static function forKey(string $key): self
    {
        return static::firstOrNew(['key' => $key]);
    }
}
