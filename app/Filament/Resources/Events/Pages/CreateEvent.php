<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Concerns\EnforcesEventOwnership;
use App\Filament\Resources\Events\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    use EnforcesEventOwnership;

    protected static string $resource = EventResource::class;
}
