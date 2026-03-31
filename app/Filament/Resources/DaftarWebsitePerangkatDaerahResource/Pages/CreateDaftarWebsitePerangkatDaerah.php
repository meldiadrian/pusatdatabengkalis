<?php

namespace App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Pages;

use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDaftarWebsitePerangkatDaerah extends CreateRecord
{
    protected static string $resource = DaftarWebsitePerangkatDaerahResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public function getHeading(): string
    {
        return '';
    }
}
