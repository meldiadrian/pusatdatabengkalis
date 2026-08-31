<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\UserResource\Widgets\StatistikUser;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    protected ?string $heading = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data')
                ->visible(
                    fn() =>
                    auth()->check() &&
                        in_array(auth()->user()->email, [
                            'desapenebal@desa.id',
                            'desaapiapi@desa.id',
                            'diskominfo@admin.com',
                            'setda@admin.com',
                            'admin@admin.com',
                            'superadmin@mail.com',
                        ])
                )
            //->visible(fn() => auth()->user()?->email !== 'sekre@admin.com'),

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikUser::class,
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
