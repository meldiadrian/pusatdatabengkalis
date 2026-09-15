<?php

namespace App\Filament\Resources\DaftarWebsiteDesaResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\DaftarWebsiteDesa;
use App\Models\Desa;
use App\Models\Kecamatan;
use PhpParser\Node\Stmt\Label;

class StatistikWebsiteDesa extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    // protected function getStats(): array
    // {
    //     return [
    //         Card::make('Total Website', DaftarWebsiteDesa::count())
    //             ->description('Jumlah seluruh data')
    //             ->color('primary'),

    //         Card::make('Aktif', DaftarWebsiteDesa::where('status', 'aktif')->count())
    //             ->color('success'),

    //         Card::make('Tidak Aktif', DaftarWebsiteDesa::where('status', 'tidak aktif')->count())
    //             ->color('danger'),
    //     ];
    // }
    protected function getStats(): array
    {
        $user        = auth()->user();
        $isAdmin     = $user?->role === 'admin';
        $unitKerjaId = $user?->unit_kerja_id;

        // Selalu count semua kecamatan terdaftar (tidak difilter per role)
        $total = DaftarWebsiteDesa::query()
            ->select('kecamatan_id')
            ->get()
            ->pluck('kecamatan_id')
            ->flatten()
            ->unique()
            ->count();

        if ($isAdmin) {
            // Admin: jumlah tipe desa unik dari semua data
            $totalDesa = DaftarWebsiteDesa::query()
                ->select('tipe')
                ->get()
                ->pluck('tipe')
                ->unique()
                ->count();

            // Admin: jumlah website aktif dari semua data
            $totalAktif = DaftarWebsiteDesa::query()
                ->select('websitedesa')
                ->get()
                ->pluck('websitedesa')
                ->unique()
                ->count();
        } else {
            // User / Sekre: hanya data milik unit kerja sendiri
            $totalDesa = DaftarWebsiteDesa::query()
                ->where('unit_kerja_id', $unitKerjaId)
                ->select('tipe')
                ->get()
                ->pluck('tipe')
                ->unique()
                ->count();

            $totalAktif = DaftarWebsiteDesa::query()
                ->where('unit_kerja_id', $unitKerjaId)
                ->select('websitedesa')
                ->get()
                ->pluck('websitedesa')
                ->unique()
                ->count();
        }


        return [
            Stat::make('Kecamatan', $total)
                ->icon('heroicon-m-map-pin')
                ->description('Jumlah keseluruhan Kecamatan terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    'class' => '!bg-blue-600 text-white',
                ]),

            Stat::make(' Desa', $totalDesa)
                ->icon('heroicon-m-map-pin')
                ->description('Jumlah keseluruhan Desa terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    'class' => 'text-white',
                ]),

            Stat::make('Website Aktif', $totalAktif)
                ->icon('heroicon-m-globe-alt')
                ->description('Jumlah keseluruhan Website aktif')
                ->extraAttributes([

                    'style' =>  'border-left: 8px solid #4ade80;  border-radius:12px;',
                    'class' => 'text-white',
                ]),
        ];
    }
}
