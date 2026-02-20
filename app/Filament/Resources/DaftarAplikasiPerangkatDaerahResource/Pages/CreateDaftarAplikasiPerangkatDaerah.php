<?php

namespace App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Models\DaftarAplikasiPerangkatDaerah;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource;



class CreateDaftarAplikasiPerangkatDaerah extends CreateRecord
{


    public function getHeading(): string
    {
        return '';
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return null; // matikan notif bawaan
    }


    protected static string $resource = DaftarAplikasiPerangkatDaerahResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set instansi_pemohon dari unit_kerja_id user yang login
        $data['instansi_pemohon'] = auth()->user()->unit_kerja_id;
        return $data;
    }

    // protected function handleRecordCreation(array $data): \App\Models\Pemohon
    // {
    //     // Memastikan instansi_pemohon diset sebelum create
    //     $data['instansi_pemohon'] = auth()->user()->unit_kerja_id;
    //     return parent::handleRecordCreation($data);
    // }s

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['unit_kerja_id'] = auth()->user()->unit_kerja_id;
        return $data;
    }

    protected function handleRecordCreation(array $data): DaftarAplikasiPerangkatDaerah
    {
        return DaftarAplikasiPerangkatDaerah::create($data);
    }
}
