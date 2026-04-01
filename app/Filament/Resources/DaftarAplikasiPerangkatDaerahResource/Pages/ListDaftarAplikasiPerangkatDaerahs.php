<?php

namespace App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets\StatistikAplikasiOpd;

class ListDaftarAplikasiPerangkatDaerahs extends ListRecords
{
    protected static string $resource = DaftarAplikasiPerangkatDaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data')
                // ->visible(fn() => auth()->user()?->unitKerja?->tipe === 'OPD'),
                ->visible(
                    fn($record) => (
                        auth()->user()->role === 'user' &&
                        auth()->user()->unitKerja?->tipe === 'OPD'
                    )
                        ||
                        ($record && $record->user_id === auth()->id())
                ),


        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikAplikasiOpd::class,
        ];
    }
}
