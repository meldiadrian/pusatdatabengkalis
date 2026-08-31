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
            Stat::make('Organisasi Perangkat Daerah', $total)
                ->icon('heroicon-m-building-office')
                ->description('Jumlah keseluruhan OPD terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    'class' => '!bg-blue-600 text-white',
                ]),

            Stat::make('Website', $totalWebOpd)
                ->icon('heroicon-m-globe-alt')
                ->description('Jumlah keseluruhan Website terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    'class' => 'text-white',
                ]),

            Stat::make('Website Aktif', $totalAktif)
                ->icon('heroicon-m-check-badge')
                ->description('Jumlah keseluruhan Website aktif')
                ->extraAttributes([
                    'style' =>  'border-left: 8px solid #4ade80;  border-radius:12px;',
                    'class' => 'text-white',
                ]),
        ];
    }
}
