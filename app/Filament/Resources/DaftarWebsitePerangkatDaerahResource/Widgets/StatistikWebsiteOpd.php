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

        $total = WebsitePerangkatDaerah::query()
            ->whereHas('unitKerja', fn($q) => $q->where('tipe', 'OPD'))
            ->count();


        $totalWebOpd = WebsitePerangkatDaerah::query()
            ->select('websiteopd')
            ->get()
            ->pluck('websiteopd')
            ->flatten() // karena array/json
            ->unique()
            ->count();

        $totalAktif = WebsitePerangkatDaerah::where('status', 'aktif')->count();
        return [
            Stat::make('Total Organisasi Perangkat Daerah ', $total)
                ->icon('heroicon-s-map-pin')
                ->description('Jumlah Keseluruhan OPD Terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(59, 130, 246, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-blue-600 !text-white',

                ]),

            Stat::make('Total Website', $totalWebOpd)
                ->icon('heroicon-s-globe-alt')
                ->description('Jumlah Keseluruhan Website Terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-red-500 !text-white',
                ]),

            Stat::make('Total Website Aktif', $totalAktif)
                ->icon('heroicon-s-globe-alt')
                ->description('Jumlah Keseluruhan Website yang Aktif')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #4ade80; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-green-500 !text-white', 
                ]),

        ];
    }
}
