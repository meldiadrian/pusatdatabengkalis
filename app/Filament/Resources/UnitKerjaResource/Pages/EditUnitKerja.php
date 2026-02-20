<?php

namespace App\Filament\Resources\UnitKerjaResource\Pages;

use App\Filament\Resources\UnitKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUnitKerja extends EditRecord
{
    protected static string $resource = UnitKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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
        $this->redirect($this->getResource()::getUrl('index')); //untuk memberikan redirect setelah penyimpanan
    }
}
