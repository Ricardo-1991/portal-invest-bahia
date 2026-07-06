<?php

namespace App\Filament\Resources\PageContents\Pages;

use App\Filament\Concerns\LoadsTranslations;
use App\Filament\Resources\PageContents\PageContentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPageContent extends EditRecord
{
    use LoadsTranslations;

    protected static string $resource = PageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
