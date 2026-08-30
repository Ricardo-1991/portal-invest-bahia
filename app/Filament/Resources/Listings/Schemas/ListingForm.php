<?php

namespace App\Filament\Resources\Listings\Schemas;

use App\Filament\Support\LocaleTabs;
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
                            ->minValue(0)
                            ->maxValue('9999999999999.99')
                            ->validationMessages([
                                'min' => 'O preço não pode ser negativo.',
                                'max' => 'O preço não pode ser maior que R$ 9.999.999.999.999,99.',
                            ])
                            ->prefix('R$'),

                        TextInput::make('area')
                            ->label('Área (ha)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue('9999999999.99')
                            ->validationMessages([
                                'min' => 'A área não pode ser negativa.',
                                'max' => 'A área não pode ser maior que 9.999.999.999,99 hectares.',
                            ])
                            ->suffix('ha')
                            ->helperText('Área da propriedade em hectares; habilita o preço por hectare no site.'),

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
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->validationMessages([
                                'unique' => 'Já existe um classificado com este slug. Escolha outro.',
                            ])
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
                            ->appendFiles()
                            ->maxParallelUploads(1)
                            ->reorderable()
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(8192)
                            ->maxFiles(12)
                            ->placeholder('Arraste as imagens ou clique para selecionar')
                            ->validationMessages([
                                'max' => 'A galeria aceita no máximo 12 imagens e cada arquivo deve ter até 8 MB.',
                                'mimetypes' => 'Use somente imagens JPEG, PNG ou WebP.',
                            ])
                            ->helperText('Selecione uma ou várias imagens. Formatos JPEG, PNG ou WebP, até 8 MB por arquivo.'),
                    ]),
            ]);
    }
}
