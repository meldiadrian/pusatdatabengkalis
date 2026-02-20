<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\SuratPermohonanStatsWidget;
use App\Filament\Widgets\SuratBalasanStatsWidget;
use App\Models\User;


class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            SuratPermohonanStatsWidget::class,
            SuratBalasanStatsWidget::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return 2;
    }
}
