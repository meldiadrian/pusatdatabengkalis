<?php

namespace App\Filament\Resources\PemohonResource\Pages;

use App\Filament\Resources\PemohonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PemohonResource\Widgets\StatistikPemohon;


// class ListPemohons extends ListRecords
// {
//     protected static string $resource = PemohonResource::class;
//     // Hilangkan judul
//     public function getTitle(): string
//     {
//         return '';
//     }

//     // Hilangkan breadcrumb
//     public function getBreadcrumbs(): array
//     {
//         return [];
//     }


//     protected function getHeaderActions(): array
//     {
//         return [
//             Actions\CreateAction::make()
//                 ->label('Tambah Data'),
//         ];
//     }


//     protected function getHeaderWidgets(): array
//     {
//         return [
//             StatistikPemohon::class,
//         ];
//     }
// }

//----------------------------------------------------------

class ListPemohons extends ListRecords
{
    protected static string $resource = PemohonResource::class;

    protected ?string $heading = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikPemohon::class,
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}