<?php

namespace App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Widgets;

use App\Models\UnitKerja;
use Illuminate\Support\Facades\DB;
use App\Models\WebsitePerangkatDaerah;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;


class StatistikWebsiteOpd extends BaseWidget
{



    protected function getStats(): array
    {
        // $total = WebsitePerangkatDaerah::query()
        //     ->whereHas('unitKerja', function ($q) {
        //         $q->where('tipe', 'OPD');
        //     })
        //     ->count(); // ✅ ini akan jadi 3

        $total = WebsitePerangkatDaerah::query()
            ->whereHas('unitKerja', fn($q) => $q->where('tipe', 'OPD'))
            ->count();
        // $total = WebsitePerangkatDaerah::query()
        //     //     ->get()
        //     //     ->pluck('kecamatan_id')
        //     //     ->flatten() // karena array/json
        //     //     ->unique()
        //     //     ->count();

        //     ->select('unit_kerja_id')
        //     ->get()
        //     ->pluck('unit_kerja_id')
        //     ->flatten() // karena array/json
        //     ->unique()
        //     ->count();

        $totalWebOpd = WebsitePerangkatDaerah::query()
            ->select('websiteopd')
            ->get()
            ->pluck('websiteopd')
            ->flatten() // karena array/json
            ->unique()
            ->count();

        $totalAktif = WebsitePerangkatDaerah::query()
            ->select('mode')
            ->get()
            ->pluck('mode')
            ->unique()
            ->count();

        return [
            Stat::make('Total Organisasi Perangkat Daerah ', $total)
                ->icon('heroicon-s-map-pin')
                ->description('Jumlah keseluruhan OPD yang terdaftar')
                ->extraAttributes([
                    'style' => '
           box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',
                ]),

            Stat::make('Total Website', $totalWebOpd)
                ->icon('heroicon-s-globe-alt')
                ->description('Jumlah keseluruhan website yang terdaftar')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            color: white;
            box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4);
            border-radius: 12px;',
                ]),

            Stat::make('Total Website Aktif', $totalAktif)
                ->icon('heroicon-s-globe-alt')
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
