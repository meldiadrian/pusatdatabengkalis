<?php

namespace App\Filament\Resources\KonfirmasiResource\Pages;

use App\Filament\Resources\KonfirmasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKonfirmasi extends EditRecord
{
    protected static string $resource = KonfirmasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
