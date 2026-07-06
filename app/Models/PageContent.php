<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PageContent extends Model
{
    use HasTranslations;

    /** Chaves de página válidas. */
    public const KEYS = ['home', 'fazenda', 'ativo', 'servico', 'informacoes', 'contatos'];

    protected $fillable = ['key', 'title', 'body'];

    public array $translatable = ['title', 'body'];

    /** Retorna (ou cria) o conteúdo de uma página pela chave. */
    public static function forKey(string $key): self
    {
        return static::firstOrNew(['key' => $key]);
    }
}
