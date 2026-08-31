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

        // Hanya data user login
        $totalUser = User::query()
            ->where('id', $user->id)
            ->where('role', 'user')
            ->count();

        $totalSekre = User::query()
            ->where('id', $user->id)
            ->where('role', 'sekre')
            ->count();

        return [
            Stat::make('User', $totalUser)
                ->icon('heroicon-s-users')
                ->description('Jumlah keseluruhan data user')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    'class' => '!bg-blue-600 !text-white',
                ]),

            Stat::make('Sekretaris', $totalSekre)
                ->icon('heroicon-s-users')
                ->description('Jumlah keseluruhan data sekretaris')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    'class' => '!bg-red-500 !text-white',
                ]),
        ];
    }
}