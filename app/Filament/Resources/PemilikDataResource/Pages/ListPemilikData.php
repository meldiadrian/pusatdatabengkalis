<?php

namespace App\Filament\Resources\PemilikDataResource\Pages;

use App\Filament\Resources\PemilikDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPemilikData extends ListRecords
{
    protected static string $resource = PemilikDataResource::class;
    protected ?string $heading = '';

    protected function getHeaderActions(): array
    {
        return [];
        // return [


        //     Actions\CreateAction::make()
        //      ->label('Tambah Data'),
        // ];
    }
}
