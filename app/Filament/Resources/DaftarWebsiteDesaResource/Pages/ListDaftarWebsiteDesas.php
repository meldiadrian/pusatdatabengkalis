<?php

namespace App\Filament\Resources\DaftarWebsiteDesaResource\Pages;

use App\Filament\Resources\DaftarWebsiteDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarWebsiteDesas extends ListRecords
{
    protected static string $resource = DaftarWebsiteDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data')
                ->visible(fn() => auth()->user()?->unitKerja?->tipe === 'Desa')
        ];
    }
}
