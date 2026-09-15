<?php

namespace App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Pages;

use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource;
use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Widgets\StatistikWebsiteOpd;
use App\Models\UnitKerja;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListDaftarWebsitePerangkatDaerahs extends ListRecords
{
    protected static string $resource = DaftarWebsitePerangkatDaerahResource::class;
    protected ?string $heading = '';

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make()
            // ->label('Tambah Data'),

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
                    $url = route('pdf.daftar-website-opd');
                    if (!empty($data['unit_kerja_id'])) {
                        $url .= '?unit_kerja_id=' . $data['unit_kerja_id'];
                    }
                    $this->redirect($url, navigate: false);
                })
                ->modalHeading('Download PDF Daftar Website OPD')
                ->modalSubmitActionLabel('Download PDF')
                ->modalWidth('md'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikWebsiteOpd::class,
        ];
    }
}
