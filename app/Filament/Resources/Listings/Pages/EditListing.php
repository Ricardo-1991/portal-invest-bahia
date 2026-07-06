<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Filament\Concerns\GuardsListingPublication;
use App\Filament\Concerns\LoadsTranslations;
use App\Filament\Resources\Listings\ListingResource;
use App\Filament\Resources\Listings\Tables\ListingsTable;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditListing extends EditRecord
{
    use GuardsListingPublication, LoadsTranslations;

    protected static string $resource = ListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ListingsTable::pdfAction(),
            DeleteAction::make(),
        ];
    }
}
