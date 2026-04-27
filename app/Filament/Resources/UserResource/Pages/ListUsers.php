<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
                        ])
                )
            //->visible(fn() => auth()->user()?->email !== 'sekre@admin.com'),

        ];
    }
}
