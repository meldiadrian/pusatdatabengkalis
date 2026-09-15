<?php

namespace App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets\StatistikAplikasiOpd;
use App\Models\UnitKerja;
use Filament\Forms\Components\Select;

class ListDaftarAplikasiPerangkatDaerahs extends ListRecords
{
    protected static string $resource = DaftarAplikasiPerangkatDaerahResource::class;
    protected ?string $heading = null;


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
                ),

            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Select::make('unit_kerja_id')
                        ->label('Filter Berdasarkan OPD')
                        ->placeholder('-- Semua OPD --')
                        ->options(
                            UnitKerja::where('tipe', 'OPD')
                                ->orderBy('nama_opd')
                                ->pluck('nama_opd', 'id')
                        )
                        ->searchable()
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    $url = route('pdf.daftar-aplikasi-opd');
                    if (!empty($data['unit_kerja_id'])) {
                        $url .= '?unit_kerja_id=' . $data['unit_kerja_id'];
                    }
                    $this->redirect($url, navigate: false);
                })
                ->modalHeading('Download PDF Daftar Aplikasi OPD')
                ->modalSubmitActionLabel('Download PDF')
                ->modalWidth('md'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikAplikasiOpd::class,
        ];
    }
    public function getBreadcrumbs(): array
    {
        return [];
    }
}
