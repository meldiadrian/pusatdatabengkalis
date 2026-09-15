<?php

namespace App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets;

use App\Models\DaftarAplikasiPerangkatDaerah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikAplikasiOpd extends BaseWidget
{

    protected function getStats(): array
    {
        $user       = auth()->user();
        $isAdmin    = $user?->role === 'admin';
        $unitKerjaId = $user?->unit_kerja_id;

        // Base query — filter tipe OPD
        $baseQuery = fn() => DaftarAplikasiPerangkatDaerah::whereHas('unitKerja', fn($q) => $q->where('tipe', 'OPD'));

        // Selalu count semua OPD terdaftar (tidak difilter per role)
        $total = $baseQuery()->distinct('unit_kerja_id')->count('unit_kerja_id');

        if ($isAdmin) {
            // Admin: jumlah aplikasi dari semua OPD
            $totalAplikasi = $baseQuery()
                ->select('unit_kerja_id')
                ->distinct('unit_kerja_id')
                ->count('unit_kerja_id');

            // Admin: jumlah aplikasi aktif (semua OPD)
            $totalAktif = $baseQuery()->where('status', 'aktif')->count();
        } else {
            // User / Sekre: hanya data milik unit kerja sendiri
            $totalAplikasi = $baseQuery()->where('unit_kerja_id', $unitKerjaId)->count();

            $totalAktif = $baseQuery()
                ->where('unit_kerja_id', $unitKerjaId)
                ->where('status', 'aktif')
                ->count();
        }

        return [
            Stat::make('Organisasi Perangkat Daerah ', $total)
                ->icon('heroicon-s-circle-stack')
                ->description('Jumlah Keseluruhan Unit Kerja yang Terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(59, 130, 246, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-blue-600 !text-white',
                ]),

            Stat::make('Aplikasi Organisasi Perangkat Daerah', $totalAplikasi)
                ->icon('heroicon-o-map')
                ->description('Jumlah Keseluruhan Aplikasi yang Terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(255, 193, 7, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-yellow-500 !text-white',

                ]),

            Stat::make('Aplikasi Aktif', $totalAktif)
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
