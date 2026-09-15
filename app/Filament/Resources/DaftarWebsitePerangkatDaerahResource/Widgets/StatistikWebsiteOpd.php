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
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user        = auth()->user();
        $isAdmin     = $user?->role === 'admin';
        $unitKerjaId = $user?->unit_kerja_id;

        // Base query — filter tipe OPD
        $baseQuery = fn() => WebsitePerangkatDaerah::query()
            ->whereHas('unitKerja', fn($q) => $q->where('tipe', 'OPD'));

        // Selalu count semua OPD terdaftar (tidak difilter per role)
        $total = $baseQuery()->distinct('unit_kerja_id')->count('unit_kerja_id');

        if ($isAdmin) {
            // Admin: jumlah website unik dari semua OPD
            $totalWebOpd = $baseQuery()
                ->select('websiteopd')
                ->distinct('websiteopd')
                ->count('websiteopd');

            // Admin: jumlah website aktif dari semua OPD
            $totalAktif = $baseQuery()->where('status', 'aktif')->count();
        } else {
            // User / Sekre: hanya data milik unit kerja sendiri
            $totalWebOpd = $baseQuery()
                ->where('unit_kerja_id', $unitKerjaId)
                ->select('websiteopd')
                ->distinct('websiteopd')
                ->count('websiteopd');

            $totalAktif = $baseQuery()
                ->where('unit_kerja_id', $unitKerjaId)
                ->where('status', 'aktif')
                ->count();
        }


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
