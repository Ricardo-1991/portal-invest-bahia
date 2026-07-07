<?php

namespace App\Filament\Resources\Listings\Tables;

use App\Models\Listing;
use App\Support\ListingPdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('main')
                    ->label('Imagem')
                    ->getStateUsing(fn ($record) => $record->mainImageUrl('thumb'))
                    ->square(),

                TextColumn::make('title')
                    ->label('Título')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', app()->getLocale(), false)
                        ?: $record->getTranslation('title', 'pt', false))
                    ->searchable(query: fn ($query, string $search) => $query->whereRaw('title::text ilike ?', ["%{$search}%"]))
                    ->limit(40),

                TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                TextColumn::make('status')
                    ->label('Situação')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'published' => 'success',
                        'hidden' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'published' => 'Publicado',
                        'hidden' => 'Oculto',
                        default => 'Rascunho',
                    }),

                TextColumn::make('region')
                    ->label('Região')
                    ->toggleable(),

                TextColumn::make('area')
                    ->label('Área (ha)')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ha')
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Corretor')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'fazenda' => 'Fazenda',
                        'ativo' => 'Ativo',
                        'servico' => 'Serviço',
                    ]),
                SelectFilter::make('status')
                    ->label('Situação')
                    ->options([
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'hidden' => 'Oculto',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                self::pdfAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    /** Ação de geração de PDF interno do classificado (somente admin). */
    public static function pdfAction(): Action
    {
        return Action::make('pdf')
            ->label('Gerar PDF')
            ->icon(Heroicon::OutlinedDocumentArrowDown)
            ->color('gray')
            ->action(fn (Listing $record) => response()->streamDownload(
                fn () => print (ListingPdf::for($record)->output()),
                ListingPdf::filename($record),
            ));
    }
}
