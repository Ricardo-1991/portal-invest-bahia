<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Evento')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order')
                            ->label('Ordem de exibição')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_published')
                            ->label('Publicado')
                            ->default(true),

                        // Dono do evento: o admin escolhe; o corretor recebe o seu id automaticamente.
                        Select::make('user_id')
                            ->label('Corretor responsável')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn () => auth()->user()?->isAdmin())
                            ->default(fn () => auth()->id()),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->label('Imagem')
                            ->collection('image')
                            ->image()
                            // Restringe a formatos raster (exclui SVG: risco de XSS armazenado
                            // se um arquivo malicioso for aberto direto pela URL do storage).
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(8192)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                Section::make('Conteúdo por idioma')
                    ->schema([
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("title.{$locale}")
                                ->label('Título')
                                ->required($locale === 'pt')
                                ->maxLength(255),

                            TextInput::make("subtitle.{$locale}")
                                ->label('Subtítulo')
                                ->maxLength(255),

                            Textarea::make("description.{$locale}")
                                ->label('Descrição')
                                ->rows(5),
                        ]),
                    ]),
            ]);
    }
}
