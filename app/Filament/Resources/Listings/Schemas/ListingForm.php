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
                            ->helperText('Só é possível publicar com os 4 idiomas preenchidos.'),

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
                            ->helperText('Deixe em branco para gerar a partir do título em português.'),
                    ]),

                Section::make('Conteúdo por idioma')
                    ->schema([
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("title.{$locale}")
                                ->label('Título')
                                ->required($locale === 'pt')
                                ->maxLength(255)
                                // Gera o slug a partir do título em português, se ainda vazio.
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state, $get) use ($locale) {
                                    if ($locale === 'pt' && blank($get('slug')) && filled($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            TextInput::make("subtitle.{$locale}")
                                ->label('Subtítulo')
                                ->maxLength(255),

                            Textarea::make("description.{$locale}")
                                ->label('Descrição')
                                ->required($locale === 'pt')
                                ->rows(6),
                        ]),
                    ]),

                Section::make('Imagens')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('main')
                            ->label('Imagem principal')
                            ->collection('main')
                            ->image()
                            ->imageEditor(),

                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Galeria')
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->image(),
                    ]),
            ]);
    }
}
