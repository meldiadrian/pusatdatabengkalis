<?php

namespace App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Pages;

use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource;
use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Widgets\StatistikWebsiteOpd;
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

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikWebsiteOpd::class,
        ];
    }
}
