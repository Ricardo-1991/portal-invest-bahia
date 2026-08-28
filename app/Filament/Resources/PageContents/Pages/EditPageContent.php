<?php

namespace App\Filament\Resources\PageContents\Pages;

use App\Filament\Concerns\ConfirmsSave;
use App\Filament\Resources\PageContents\PageContentResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class EditPageContent extends EditRecord
{
    use ConfirmsSave;

    protected static string $resource = PageContentResource::class;

    /** Impede que uma divergência de configuração de upload vire erro 500. */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (FileIsTooBig) {
            $message = 'O vídeo excede o limite de 100 MB. Comprima o arquivo e tente novamente.';

            $this->addError('data.hero_video', $message);

            Notification::make()
                ->title('Vídeo muito grande')
                ->body($message)
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
