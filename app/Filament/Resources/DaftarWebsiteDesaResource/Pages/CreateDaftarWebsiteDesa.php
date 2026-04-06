<?php

namespace App\Filament\Resources\DaftarWebsiteDesaResource\Pages;

use App\Filament\Resources\DaftarWebsiteDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDaftarWebsiteDesa extends CreateRecord
{
    protected static string $resource = DaftarWebsiteDesaResource::class;


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public function getHeading(): string
    {
        return '';
    }
    public static function canCreate(): bool
    {
        return !auth()->user()->isOPD();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
