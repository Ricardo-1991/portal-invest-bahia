<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Concerns\ConfirmsSave;
use App\Filament\Concerns\EnforcesEventOwnership;
use App\Filament\Concerns\LoadsTranslations;
use App\Filament\Resources\Events\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    use ConfirmsSave, EnforcesEventOwnership, LoadsTranslations;

    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
