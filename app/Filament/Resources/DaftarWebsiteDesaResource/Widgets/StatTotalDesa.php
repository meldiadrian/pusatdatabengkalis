<?php

namespace App\Filament\Resources\DaftarWebsiteDesaResource\Widgets;

use App\Models\DaftarWebsiteDesa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\UnitKerja;


class StatTotalDesa extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $total = UnitKerja::where('tipe', 'Desa')->count();

        return [
            Stat::make('Total Desa', $total)
                ->icon('heroicon-s-circle-stack')
                ->description('Jumlah Keseluruhan Desa yang Terdaftar')
                ->extraAttributes([
                    'style' => '
            box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4);
                        border-radius: 12px;
                    ',
                    'class' => '!bg-blue-600 !text-white',
                ]),
        ];
    }
}
