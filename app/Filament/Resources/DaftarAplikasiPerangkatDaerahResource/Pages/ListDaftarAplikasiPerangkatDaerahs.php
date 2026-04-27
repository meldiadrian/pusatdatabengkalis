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
               
                // ->visible(
                //     fn($record) => (
                //         auth()->user()->role === 'user' &&
                //         auth()->user()->unitKerja?->tipe === 'OPD'
                //     )
                //         ||
                //         ($record && $record->user_id === auth()->id())
                // ),
//---------------------------------------------------------------------------------------------
                // ->visible(
                //     fn($record) =>
                //     auth()->user()?->email !== 'sekre@admin.com'
                //         &&
                //         (
                //             (
                //                 auth()->user()->role === 'user' &&
                //                 auth()->user()->unitKerja?->tipe === 'OPD'
                //             )
                //             ||
                //             ($record && $record->user_id === auth()->id())
                //         )
                // )


                ->visible(
                    fn($record) =>
                    auth()->user()?->email === 'admin@admin.com'
                        ||
                        (
                            auth()->user()?->email !== 'sekre@admin.com'
                            &&
                            (
                                (
                                    auth()->user()->role === 'user' &&
                                    auth()->user()->unitKerja?->tipe === 'OPD'
                                )
                                ||
                                ($record && $record->user_id === auth()->id())
                            )
                        )
                )
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikAplikasiOpd::class,
        ];
    }
}
