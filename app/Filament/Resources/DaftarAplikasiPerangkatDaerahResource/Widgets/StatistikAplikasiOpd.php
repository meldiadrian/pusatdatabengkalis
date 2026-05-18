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

        $totalAplikasi = DaftarAplikasiPerangkatDaerah::query()
            ->select('nama_aplikasi', 'unit_kerja_id')
            ->whereHas('unitKerja', function ($q) {
                $q->where('tipe', 'OPD');
            })
            ->get()
            ->pluck('unit_kerja_id')
            ->flatten()
            ->unique()
            ->count();

        $totalAktif = DaftarAplikasiPerangkatDaerah::where('status', 'aktif')->count();

        return [
            Stat::make('Total Organisasi Perangkat Daerah ', $total)
                ->icon('heroicon-s-circle-stack')
                ->description('Jumlah Keseluruhan Unit Kerja yang Terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(59, 130, 246, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-blue-600 !text-white',
                ]),

            Stat::make('Total Aplikasi Organisasi Perangkat Daerah', $totalAplikasi)
                ->icon('heroicon-o-map')
                ->description('Jumlah Keseluruhan Aplikasi yang Terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(255, 193, 7, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-yellow-500 !text-white',

                ]),

            Stat::make('Total Aplikasi Aktif', $totalAktif)
                ->icon('heroicon-o-map')
                ->description('Jumlah Keseluruhan Aplikasi yang Aktif')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #4ade80; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-green-500 !text-white',
                ]),


        ];
    }
}
