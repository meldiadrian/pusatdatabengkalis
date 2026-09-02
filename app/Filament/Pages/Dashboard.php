<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\CustomAccountWidget;

use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets\StatAplikasiOpd;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets\StatistikAplikasiOpd;
use App\Filament\Resources\DaftarWebsiteDesaResource\Widgets\StatTotalDesa;
use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Widgets\StatistikWebsiteOpd;
use App\Filament\Resources\DaftarWebsiteDesaResource\Widgets\StatistikWebsiteDesa;



class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';


    public function getWidgets(): array
    {
        return [
            CustomAccountWidget::class,
            // StatAplikasiOpd::class,
            // StatTotalDesa::class,
            StatistikWebsiteOpd::class,
            StatistikWebsiteDesa::class,

        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'sm'      => 1,
            'md'      => 2,
            'xl'      => 3,
        ];
    }
}
