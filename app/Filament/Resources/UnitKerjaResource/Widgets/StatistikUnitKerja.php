<?php

namespace App\Filament\Resources\UnitKerjaResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\UnitKerja;

class StatistikUnitKerja extends BaseWidget
{
    protected function getStats(): array
    {
        // Total OPD
        $totalOPD = UnitKerja::where('tipe', 'OPD')->count();

        // Total aplikasi (anggap 1 record = 1 aplikasi)
        $totalDesa = UnitKerja::where('tipe', 'Desa')->count();

        // Total aplikasi aktif
        //$totalAktif = UnitKerja::where('status', 'aktif')->count();

        return [
            Stat::make('Total Organisasi Perangkat Daerah', $totalOPD)
                ->icon('heroicon-o-map')
                ->description('Jumlah keseluruhan unit kerja OPD')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    'class' => '!bg-blue-600 text-white',
                ]),

            Stat::make('Total Desa', $totalDesa)
                ->icon('heroicon-o-map')
                ->description('Jumlah Keseluruhan Desa')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    'class' => 'text-white',
                ]),

            // Stat::make('Total Aplikasi Aktif', $totalAktif)
            //     ->icon('heroicon-o-check-circle')
            //     ->description('Jumlah aplikasi aktif')
            //     ->extraAttributes([
            //         'style' => 'box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4); border-radius:12px;',
            //         'class' => '!bg-green-600 text-white',
            //     ]),
        ];
    }
}
