<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StatistikUser extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $stats = [];

        // Role user: hanya tampil widget User
        if ($user->role === 'user') {
            $totalUser = User::where('role', 'user')->count();

            $stats[] = Stat::make('User', $totalUser)
                ->icon('heroicon-s-users')
                ->description('Jumlah keseluruhan data user')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    'class' => '!bg-blue-600 !text-white',
                ]);
        }

        // Role sekre: hanya tampil widget Sekretaris
        if ($user->role === 'sekre') {
            $totalSekre = User::where('role', 'sekre')->count();

            $stats[] = Stat::make('Sekretaris', $totalSekre)
                ->icon('heroicon-s-users')
                ->description('Jumlah keseluruhan data sekretaris')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    'class' => '!bg-red-500 !text-white',
                ]);
        }

        // Role admin: tampil keduanya
        if ($user->role === 'admin') {
            $stats[] = Stat::make('User', User::where('role', 'user')->count())
                ->icon('heroicon-s-users')
                ->description('Jumlah keseluruhan data user')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    'class' => '!bg-blue-600 !text-white',
                ]);

            $stats[] = Stat::make('Sekretaris', User::where('role', 'sekre')->count())
                ->icon('heroicon-s-users')
                ->description('Jumlah keseluruhan data sekretaris')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    'class' => '!bg-red-500 !text-white',
                ]);
        }

        return $stats;
    }
}