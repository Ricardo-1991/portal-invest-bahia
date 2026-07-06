<?php

namespace App\Filament\Resources\PageContents\Schemas;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Página')
                    ->schema([
                        Select::make('key')
                            ->label('Página')
                            ->options([
                                'home' => 'Início',
                                'fazenda' => 'Fazenda',
                                'ativo' => 'Ativo',
                                'servico' => 'Serviço',
                                'informacoes' => 'Informações',
                                'contatos' => 'Contatos',
                            ])
                            ->required()
                            ->unique(ignoreRecord: true)
                            // Não permite trocar a chave de uma página já criada.
                            ->disabledOn('edit'),
                    ]),

                Section::make('Conteúdo por idioma')
                    ->schema([
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("title.{$locale}")
                                ->label('Título')
                                ->required($locale === 'pt')
                                ->maxLength(255),

                            RichEditor::make("body.{$locale}")
                                ->label('Conteúdo'),
                        ]),
                    ]),
            ]);
    }
}
