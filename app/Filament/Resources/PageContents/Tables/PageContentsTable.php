<?php

namespace App\Filament\Resources\PageContents\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageContentsTable
{
    /** Rótulos amigáveis das páginas. */
    public const LABELS = [
        'home' => 'Início',
        'fazenda' => 'Fazenda',
        'ativo' => 'Ativo',
        'servico' => 'Serviço',
        'informacoes' => 'Informações',
        'contatos' => 'Contatos',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Página')
                    ->formatStateUsing(fn (string $state) => self::LABELS[$state] ?? $state)
                    ->badge(),

                TextColumn::make('title')
                    ->label('Título (idioma atual)')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', app()->getLocale(), false)
                        ?: $record->getTranslation('title', 'pt', false)),

                TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
