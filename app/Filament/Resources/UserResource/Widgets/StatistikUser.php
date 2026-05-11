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
                    'style' => 'box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6); border-radius:12px;',
                    'class' => '!bg-blue-600 text-white',
                ]),

            Stat::make('Total berdasarkan role sekre', $totalSekre)
                ->icon('heroicon-s-users')
                ->description('Jumlah Keseluruhan Sekretaris')
                ->extraAttributes([
                    'style' => 'box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4); border-radius:12px;',
                    'class' => 'text-white',
                ]),
        ];
    }
}
