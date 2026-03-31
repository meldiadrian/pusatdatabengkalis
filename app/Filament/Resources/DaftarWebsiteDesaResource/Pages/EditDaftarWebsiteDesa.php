<?php

namespace App\Filament\Resources\DaftarWebsiteDesaResource\Pages;

use App\Filament\Resources\DaftarWebsiteDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaftarWebsiteDesa extends EditRecord
{
    protected static string $resource = DaftarWebsiteDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->unitKerja?->tipe === 'Desa')
        ];
    }
}
