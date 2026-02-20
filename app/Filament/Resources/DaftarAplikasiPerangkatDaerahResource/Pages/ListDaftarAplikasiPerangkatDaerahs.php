<?php

namespace App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Pages;

use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarAplikasiPerangkatDaerahs extends ListRecords
{
    protected static string $resource = DaftarAplikasiPerangkatDaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data'),

        ];
    }
}
