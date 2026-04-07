<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DaftarWebsiteDesaResource\Widgets\StatistikWebsiteDesa as WidgetsStatistikWebsiteDesa;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\SuratPermohonanStatsWidget;
use App\Filament\Widgets\SuratBalasanStatsWidget;
use App\Models\User;
use App\Filament\Resources\DaftarWebsiteResource\Widgets\StatistikWebsiteDesa;
use Filament\Widgets\AccountWidget;



class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            SuratPermohonanStatsWidget::class,
            SuratBalasanStatsWidget::class,
            \Filament\Widgets\AccountWidget::class,
            WidgetsStatistikWebsiteDesa::class,
        ];
    }


    public function getColumns(): int|string|array
    {
        return 2;
    }
}
