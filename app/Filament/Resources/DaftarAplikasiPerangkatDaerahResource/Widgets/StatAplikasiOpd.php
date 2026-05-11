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
            Stat::make('Total Organisasi Perangkat Daerah', $total)
                ->icon('heroicon-s-circle-stack')
                ->description('Jumlah Keseluruhan Unit Kerja yang Terdaftar')
                ->extraAttributes([
                    'style' => '
                        box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6);
                        border-radius: 12px;
                    ',
                    'class' => '!bg-blue-600 !text-white',
                ]),
        ];
    }
}
