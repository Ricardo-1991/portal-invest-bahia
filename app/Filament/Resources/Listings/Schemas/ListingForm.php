<?php

namespace App\Filament\Resources\Listings\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Models\Listing;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do classificado')
                    ->columns(2)
                    ->schema([
                        Select::make('category')
                            ->label('Categoria')
                            ->options([
                                'fazenda' => 'Fazenda',
                                'ativo' => 'Ativo',
                                'servico' => 'Serviço',
                            ])
                            ->required(),

                        Select::make('status')
                            ->label('Situação')
                            ->options([
                                'draft' => 'Rascunho',
                                'published' => 'Publicado',
                                'hidden' => 'Oculto',
                            ])
                            ->default('draft')
                            ->required()
                            ->helperText('Preencha título e descrição em pelo menos um idioma para publicar.'),

                        TextInput::make('region')
                            ->label('Região / Localização')
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Preço (R$)')
                            ->numeric()
                            ->prefix('R$'),

                        // Dono do anúncio: o admin escolhe; o corretor recebe o seu id automaticamente.
                        Select::make('user_id')
                            ->label('Corretor responsável')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn () => auth()->user()?->isAdmin())
                            ->default(fn () => auth()->id()),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->maxLength(255)
                            ->helperText('Deixe em branco para gerar automaticamente a partir do título preenchido.'),
                    ]),

                Section::make('Conteúdo por idioma')
                    ->schema([
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("title.{$locale}")
                                ->label('Título')
                                ->maxLength(255)
                                // Gera o slug a partir do primeiro título preenchido, se ainda vazio.
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state, $get) {
                                    if (blank($get('slug')) && filled($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            TextInput::make("subtitle.{$locale}")
                                ->label('Subtítulo')
                                ->maxLength(255),

                            Textarea::make("description.{$locale}")
                                ->label('Descrição')
                                ->rows(6),
                        ]),
                    ]),

                Section::make('Imagens')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('main')
                            ->label('Imagem principal')
                            ->collection('main')
                            ->image()
                            // Restringe a formatos raster (exclui SVG: risco de XSS armazenado
                            // se um arquivo malicioso for aberto direto pela URL do storage).
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(8192)
                            ->imageEditor(),

                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Galeria')
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(8192)
                            ->maxFiles(12),
                    ]),
            ]);
    }
}
