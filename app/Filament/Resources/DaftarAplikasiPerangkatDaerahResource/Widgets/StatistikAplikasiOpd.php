<?php

namespace App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets;

use App\Models\DaftarAplikasiPerangkatDaerah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikAplikasiOpd extends BaseWidget
{

    protected function getStats(): array
    {
        // $total = DaftarAplikasiPerangkatDaerah::count();
        $total = DaftarAplikasiPerangkatDaerah::whereHas('unitKerja', function ($q) {
            $q->where('tipe', 'OPD');
        })
            ->count();
        $total = DaftarAplikasiPerangkatDaerah::whereHas('unitKerja', function ($q) {
            $q->where('tipe', 'OPD');
        })
            ->distinct('unit_kerja_id')
            ->count('unit_kerja_id');

        $totalDesa = DaftarAplikasiPerangkatDaerah::query()
            ->select('unit_kerja_id')
            ->get()
            ->pluck('unit_kerja_id')
            ->flatten() // karena array/json
            ->unique()
            ->count();

        $totalAktif = DaftarAplikasiPerangkatDaerah::whereIn('status', ['aktif', 'Aktif'])->count();

        return [
            Stat::make('Total Organisasi Perangkat Daerah ', $total)
                ->icon('heroicon-s-circle-stack')
                ->description('Jumlah keseluruhan unit kerja yang terdaftar')
                ->extraAttributes([
                    'style' => '
           box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',
                ]),

            Stat::make('Total Desa', $totalDesa)
                ->icon('heroicon-o-map')
                ->description('Jumlah keseluruhan Desa yang terdaftar')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            color: white;
            box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4);
            border-radius: 12px;',
                ]),

            Stat::make('Total Website Aktif', $totalAktif)
                ->icon('heroicon-o-map')
                ->description('Jumlah Website Aktif')
                ->extraAttributes([
                    'style' => '
                --fi-stats-card-color: #ffffff;       
               color:white;
                box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4);            
                border-radius:12px;',
                    'class' => '!bg-danger-600 !text-danger',
                ]),


        ];
    }
}
