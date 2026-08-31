<?php

namespace App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets;

use App\Models\DaftarAplikasiPerangkatDaerah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\UnitKerja;

class StatAplikasiOpd extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        // $total = DaftarAplikasiPerangkatDaerah::whereHas('unitKerja', function ($q) {
        //     $q->where('tipe', 'OPD');
        // })
        //     ->distinct('unit_kerja_id')
        //     ->count('unit_kerja_id');

        $total = UnitKerja::where('tipe', 'OPD')->count();

        return [
            Stat::make('Total OPD', $total)
                ->icon('heroicon-m-building-office')
                ->description('OPD terdaftar')
                ->color('primary'),
        ];
    }
}
