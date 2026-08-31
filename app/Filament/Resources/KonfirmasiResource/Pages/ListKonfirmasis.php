<?php

namespace App\Filament\Resources\KonfirmasiResource\Pages;

use App\Filament\Resources\KonfirmasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKonfirmasis extends ListRecords
{
    protected static string $resource = KonfirmasiResource::class;
    protected ?string $heading = '';

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),

        ];
    }
}
