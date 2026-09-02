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
