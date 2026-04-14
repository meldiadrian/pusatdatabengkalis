<?php

namespace App\Filament\Resources\UnitKerjaResource\Pages;

use App\Filament\Resources\UnitKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use App\Filament\Resources\UnitKerjaResource\Widgets\StatistikUnitKerja;


class ListUnitKerjas extends ListRecords
{
    protected static string $resource = UnitKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data'),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            StatistikUnitKerja::class,
        ];
    }
}
