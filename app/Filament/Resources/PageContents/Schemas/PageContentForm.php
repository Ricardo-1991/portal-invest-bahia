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
                Section::make('Vídeo da página inicial')
                    ->description('O vídeo aparece somente no hero da página Início.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero_video')
                            ->label('Vídeo do hero')
                            ->collection('hero_video')
                            ->acceptedFileTypes(['video/mp4'])
                            ->maxSize(config('pib.uploads.hero_video_max_kb'))
                            ->validationMessages([
                                'max' => 'O vídeo excede o limite de 100 MB. Comprima o arquivo e tente novamente.',
                                'mimetypes' => 'O vídeo deve estar no formato MP4.',
                            ])
                            ->helperText('Envie um MP4 pronto para web, com até 100 MB. Um novo envio substitui o vídeo atual.'),
                    ]),
            ]);
    }
}
