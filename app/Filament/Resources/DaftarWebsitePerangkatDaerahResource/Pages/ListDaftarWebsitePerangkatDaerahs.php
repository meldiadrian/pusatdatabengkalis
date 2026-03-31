<?php

namespace App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Pages;

use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarWebsitePerangkatDaerahs extends ListRecords
{
    protected static string $resource = DaftarWebsitePerangkatDaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make()
               // ->label('Tambah Data'),
        ];
    }
}
