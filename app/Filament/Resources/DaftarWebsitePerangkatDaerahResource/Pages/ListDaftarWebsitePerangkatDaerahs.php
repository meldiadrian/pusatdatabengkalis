<?php

namespace App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Pages;

use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource;
use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Widgets\StatistikWebsiteOpd;
use Filament\Actions;
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
                ->url(fn() => route('pdf.daftar-website-opd'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikWebsiteOpd::class,
        ];
    }
}
