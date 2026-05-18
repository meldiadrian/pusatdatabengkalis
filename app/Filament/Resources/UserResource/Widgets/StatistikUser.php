<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;


class StatistikUser extends BaseWidget
{
    protected function getStats(): array
    {
       
        $totalUser = User::where('role', 'user')->count();

     
        $totalSekre = User::where('role', 'sekre')->count();

        return [
            Stat::make('Total berdasarkan role user', $totalUser)
                ->icon('heroicon-s-users')
                ->description('Jumlah keseluruhan user')
                ->extraAttributes([
                     'style' => 'border-left: 8px solid #93c5fd; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(59, 130, 246, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-blue-600 !text-white',
                ]),

            Stat::make('Total berdasarkan role sekre', $totalSekre)
                ->icon('heroicon-s-users')
                ->description('Jumlah Keseluruhan Sekretaris')
                ->extraAttributes([
                    'style' => 'border-left: 8px solid #f87171; border-radius:12px;',
                    // 'style' => 'box-shadow: 0 -4px 6px -2px rgba(255, 193, 7, 0.4); border-radius:12px;', --- IGNORE ---
                    'class' => '!bg-red-500 !text-white',
                ]),
        ];
    }
}
