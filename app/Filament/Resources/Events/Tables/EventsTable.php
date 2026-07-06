<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Imagem')
                    ->getStateUsing(fn ($record) => $record->imageUrl('thumb'))
                    ->square(),

                TextColumn::make('title')
                    ->label('Título')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', app()->getLocale(), false)
                        ?: $record->getTranslation('title', 'pt', false))
                    ->limit(50),

                IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),

                TextColumn::make('user.name')
                    ->label('Corretor')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('order')
                    ->label('Ordem')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }
}
