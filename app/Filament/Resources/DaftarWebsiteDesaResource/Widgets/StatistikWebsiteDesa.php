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
        // $total = id::count(); // <-- ambil data dari DB
        //$total = DaftarWebsiteDesa::count();
        // $total = DaftarWebsiteDesa::query()
        //     ->select('websitedesa')
        //     ->get()
        //     ->pluck('websitedesa')
        //     ->flatten() // karena array/json
        //     ->unique()
        //     ->count();

        $total = DaftarWebsiteDesa::query()
            ->select('kecamatan_id')
            ->get()
            ->pluck('kecamatan_id')
            ->flatten() // karena array/json
            ->unique()
            ->count();
        // $totalDesa = DaftarWebsiteDesa::query()
        //     ->select('desa_ids')
        //     ->get()
        //     ->pluck('desa_ids')
        //     ->flatten() // karena array/json
        //     ->unique()
        //     ->count();

        // $totalDesa = DaftarWebsiteDesa::whereHas('unitKerja', function ($q) {
        //     $q->whereRaw('LOWER(tipe) = ?', ['Desa']);
        // })->count();

        $totalDesa = DaftarWebsiteDesa::query()
            ->select('tipe')
            ->get()
            ->pluck('tipe')
            ->unique()
            ->count();


        $totalAktif = DaftarWebsiteDesa::query()
            ->select('websitedesa')
            ->get()
            ->pluck('websitedesa')
            ->unique()
            ->count();

        return [
            Stat::make('Total Kecamatan ', $total)
                ->icon('heroicon-s-map-pin')
                ->description('Jumlah keseluruhan kecamatan yang terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(59, 130, 246, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-blue-600 !text-white',
                ]),

            Stat::make('Total Desa', $totalDesa)
                ->icon('heroicon-s-map-pin')
                ->description('Jumlah keseluruhan desa yang terdaftar')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(255, 193, 7, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-yellow-500 !text-white',
                ]),

            Stat::make('Total Website Aktif', $totalAktif)
                ->icon('heroicon-s-globe-alt')
                ->description('Jumlah Keseluruhan Website Aktif')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #4ade80; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-green-500 !text-white',
                ]),

        ];
    }
}
