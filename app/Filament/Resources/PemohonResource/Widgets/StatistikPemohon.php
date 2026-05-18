<?php

namespace App\Filament\Resources\PemohonResource\Widgets;

use App\Models\Pemohon;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatistikPemohon extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | ROLE & TIPE USER
        |--------------------------------------------------------------------------
        */

        $isAdmin = $user->hasRole('super_admin');
        $isSekre = $user->hasRole('sekre');

        $isValidTipe = in_array(
            optional($user->unitKerja)->tipe,
            ['OPD', 'Desa']
        );

        /*
        |--------------------------------------------------------------------------
        | QUERY DASAR
        |--------------------------------------------------------------------------
        */

        $query = Pemohon::query();

        /*
        |--------------------------------------------------------------------------
        | FILTER DATA BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */

        $query->when(
            !$isAdmin && ($isSekre || $isValidTipe),
            function ($query) use ($user) {

                $query->where(
                    'instansi_pemohon',
                    $user->unit_kerja_id
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | TOTAL DATA
        |--------------------------------------------------------------------------
        */

        $totalPemohon = (clone $query)->count();

        $totalPending = (clone $query)
            ->where('status', 'pending')
            ->count();

        $totalProses = (clone $query)
            ->where('status', 'proses')
            ->count();



        $totalSukses = (clone $query)
            ->where('status', 'sukses')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RETURN WIDGET
        |--------------------------------------------------------------------------
        */

        return [

            /*
            |--------------------------------------------------------------------------
            | TOTAL DATA
            |--------------------------------------------------------------------------
            */

            Stat::make('Total Data Pemohon', $totalPemohon)
                ->icon('heroicon-o-users')
                ->description('Akumulasi permohonan berdasarkan pengguna login')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    'class' => '!bg-blue-600 text-white',
                ]),

            /*
            |--------------------------------------------------------------------------
            | STATUS PENDING
            |--------------------------------------------------------------------------
            */

            Stat::make('Permohonan Pending', $totalPending)
                ->icon('heroicon-o-clock')
                ->description('Permohonan yang masih menunggu proses')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    'class' => 'text-white',
                ]),

            /*
            |--------------------------------------------------------------------------
            | STATUS PROSES
            |--------------------------------------------------------------------------
            */

            Stat::make('Permohonan Diproses', $totalProses)
                ->icon('heroicon-o-arrow-path')
                ->description('Permohonan yang sedang diproses')
                ->extraAttributes([

                    'style' => 'border-left: 8px solid #fbbf24; border-radius:12px;',
                    'class' => 'text-white',
                ]),

            /*
            |--------------------------------------------------------------------------
            | STATUS SUKSES
            |--------------------------------------------------------------------------
            */

            Stat::make('Permohonan Disetujui', $totalSukses)
                ->icon('heroicon-o-check-circle')
                ->description('Permohonan yang telah berhasil diproses')
                ->extraAttributes([
                    'style' =>  'border-left: 8px solid #4ade80;  border-radius:12px;',
                    'class' => '!bg-success-600 text-white',
                ]),
        ];
    }
}
