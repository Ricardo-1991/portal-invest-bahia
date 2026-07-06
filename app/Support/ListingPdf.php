<?php

namespace App\Support;

use App\Models\Listing;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfInstance;

/**
 * Gera o PDF interno de um classificado (uso administrativo).
 */
class ListingPdf
{
    public static function for(Listing $listing): PdfInstance
    {
        return Pdf::loadView('pdf.listing', [
            'listing' => $listing,
            'images' => self::images($listing),
        ])->setPaper('a4');
    }

    public static function filename(Listing $listing): string
    {
        return 'anuncio-'.$listing->slug.'.pdf';
    }

    /**
     * Converte as imagens (principal + galeria) em data URIs para o dompdf
     * embutir sem depender de acesso remoto/filesystem.
     *
     * @return array<int, string>
     */
    private static function images(Listing $listing): array
    {
        $media = collect();
        if ($main = $listing->getFirstMedia('main')) {
            $media->push($main);
        }
        $media = $media->merge($listing->getMedia('gallery'))->take(4);

        return $media
            ->map(function ($item) {
                $path = $item->getPath();
                if (! is_file($path)) {
                    return null;
                }

                return 'data:'.$item->mime_type.';base64,'.base64_encode((string) file_get_contents($path));
            })
            ->filter()
            ->values()
            ->all();
    }
}
