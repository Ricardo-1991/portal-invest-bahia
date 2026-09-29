<?php

namespace App\Filament\Resources\Listings\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Support\AreaUnits;
use App\Support\ListingMap;
use App\Support\MoneyInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ListingForm
{
    private const PRICE_INPUT_HANDLER = <<<'JS'
        if ($event.pibPriceMasked) return;
        const original = $el.value;
        const cursor = $el.selectionStart ?? original.length;
        const comma = original.indexOf(',');
        const inCents = comma >= 0 && cursor > comma;
        const digitsBeforeCursor = (inCents ? original.slice(comma + 1, cursor) : original.slice(0, cursor))
            .replace(/\D/g, '').length;
        const parts = original.replace(/[^\d,]/g, '').split(',');
        const digits = parts.shift();
        const cents = parts.join('').slice(0, 2);

        // Ao apagar a parte inteira, zeros que sobraram nos centavos não são um preço.
        if (!digits && (!cents || /^0+$/.test(cents))) {
            $el.value = '';
            if (original !== '') {
                const sync = new Event('input', { bubbles: true });
                sync.pibPriceMasked = true;
                $el.dispatchEvent(sync);
            }
            return;
        }

        const integer = (digits || '0').replace(/^0+(?=\d)/, '');
        const grouped = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        $el.value = grouped + (comma >= 0 ? `,${cents}` : '');

        let position = 0;
        if (inCents) {
            position = grouped.length + 1 + Math.min(digitsBeforeCursor, cents.length);
        } else {
            let seen = 0;
            while (position < grouped.length && seen < digitsBeforeCursor) {
                if (/\d/.test(grouped[position])) seen++;
                position++;
            }
        }
        $el.setSelectionRange(position, position);
        if ($el.value !== original) {
            const sync = new Event('input', { bubbles: true });
            sync.pibPriceMasked = true;
            $el.dispatchEvent(sync);
        }
        JS;

    private const PRICE_BLUR_HANDLER = <<<'JS'
        if ($el.value === '') return;
        const original = $el.value;
        const [integer, cents = ''] = original.split(',');
        $el.value = `${integer},${(cents + '00').slice(0, 2)}`;
        if ($el.value !== original) {
            const sync = new Event('input', { bubbles: true });
            sync.pibPriceMasked = true;
            $el.dispatchEvent(sync);
        }
        JS;

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
                                'apartamento' => 'Apartamento',
                                'casa' => 'Casa',
                                'sitio' => 'Sítio',
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
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step('any')
                            ->minValue(-90)
                            ->maxValue(90)
                            ->requiredWith('longitude')
                            ->live(onBlur: true)
                            ->helperText('Coordenada decimal, entre -90 e 90. Preencha junto com a longitude.'),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step('any')
                            ->minValue(-180)
                            ->maxValue(180)
                            ->requiredWith('latitude')
                            ->live(onBlur: true)
                            ->helperText('Coordenada decimal, entre -180 e 180. Preencha junto com a latitude.'),

                        Placeholder::make('map_preview')
                            ->label('Prévia da localização')
                            ->content(fn (Get $get) => view('filament.listing-map-preview', [
                                'latitude' => $get('latitude'),
                                'longitude' => $get('longitude'),
                            ]))
                            ->visible(fn (Get $get) => ListingMap::hasCoordinates($get('latitude'), $get('longitude')))
                            ->columnSpanFull(),

                        Select::make('currency')
                            ->label('Moeda')
                            ->options(['BRL' => 'Real (R$)', 'USD' => 'Dólar (US$)'])
                            ->default('BRL')
                            ->required()
                            ->live(),

                        TextInput::make('price')
                            ->label('Preço')
                            ->inputMode('decimal')
                            ->rule('numeric')
                            ->extraAlpineAttributes([
                                'x-on:input' => self::PRICE_INPUT_HANDLER,
                                'x-on:blur' => self::PRICE_BLUR_HANDLER,
                            ])
                            ->formatStateUsing(fn ($state) => MoneyInput::display($state))
                            ->mutateStateForValidationUsing(fn ($state) => MoneyInput::toDecimal($state))
                            ->dehydrateStateUsing(fn ($state) => MoneyInput::toDecimal($state))
                            ->minValue(0)
                            ->maxValue('9999999999999.99')
                            ->validationMessages([
                                'min' => 'O preço não pode ser negativo.',
                                'max' => 'O preço não pode ser maior que 9.999.999.999.999,99.',
                            ])
                            ->prefix(fn (Get $get) => $get('currency') === 'USD' ? 'US$' : 'R$')
                            ->helperText('Os milhares são separados ao digitar; os centavos aparecem ao sair do campo.'),

                        TextInput::make('area')
                            ->label('Área')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue('9999999999.99')
                            ->validationMessages([
                                'min' => 'A área não pode ser negativa.',
                                'max' => 'A área não pode ser maior que 9.999.999.999,99.',
                            ])
                            ->helperText('Informe a área na unidade selecionada.'),

                        Select::make('area_unit')
                            ->label('Unidade de área')
                            ->options(collect(array_keys(AreaUnits::SQUARE_METERS))
                                ->mapWithKeys(fn (string $unit) => [$unit => __('site.area_units.'.$unit)])
                                ->all())
                            ->default('ha')
                            ->required(),

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
