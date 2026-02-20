<?php

namespace App\Filament\Resources\PemilikDataResource\Pages;

use App\Filament\Resources\PemilikDataResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePemilikData extends CreateRecord
{
    protected static string $resource = PemilikDataResource::class;
    public function getHeading(): string
    {
        return '';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return null; // matikan notif bawaan
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->body('Data telah diperbarui dan ditampilkan.')
            ->success()
            ->send();
    }
    // protected function getCreatedNotificationTitle(): ?string
    // {
    //     return 'Data Bersahil Ditambah';
    // }
}
