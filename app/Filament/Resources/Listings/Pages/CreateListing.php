<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Filament\Concerns\ConfirmsCreation;
use App\Filament\Concerns\GuardsListingPublication;
use App\Filament\Resources\Listings\ListingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateListing extends CreateRecord
{
    use ConfirmsCreation, GuardsListingPublication;

    protected static string $resource = ListingResource::class;
}
