<?php

namespace App\Filament\Resources\PageContents\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mídia da página inicial')
                    ->description('Envie uma foto ou um vídeo para aparecer somente no hero da página Início.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero_media')
                            ->label('Foto ou vídeo do hero')
                            ->collection('hero_video')
                            ->acceptedFileTypes(['video/mp4', 'image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(config('pib.uploads.hero_video_max_kb'))
                            ->validationMessages([
                                'max' => 'A mídia excede o limite de 100 MB. Comprima o arquivo e tente novamente.',
                                'mimetypes' => 'Envie um vídeo MP4 ou uma imagem JPG, PNG ou WebP.',
                            ])
                            ->helperText('Aceita MP4, JPG, PNG ou WebP, com até 100 MB. Um novo envio substitui a mídia atual.'),
                    ]),
            ]);
    }
}
