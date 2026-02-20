<?php

namespace App\Filament\Resources\UnitKerjaResource\Pages;

use App\Filament\Resources\UnitKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
//
use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;



class CreateUnitKerja extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;
    protected static string $resource = UnitKerjaResource::class;

    public function getHeading(): string
    {
        return '';
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function getCreatedNotificationTitle(): ?string
    // {
    //     return 'Data Bersahil Ditambah';
    // }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->body('Data telah diperbarui dan ditampilkan di bawah.')
            ->success()
            ->send();
    }

    protected function getSteps(): array
    {


        return [


            Step::make('Unit Kerja')
                ->schema([
                    TextInput::make('nama_opd') //ke DB
                        ->label('Isi Unit Kerja')
                        ->required(),
                ]),


            Step::make('Alamat')
                ->schema([
                    TextInput::make('alamat') //ke DB
                        ->label('Isi Alamat')
                        ->required(),
                ]),


            Step::make('Telpon')
                ->schema([
                    TextInput::make('telp') //ke DB
                        ->label('Isi Telpon')
                        ->required()
                        ->numeric()
                        ->maxLength(12),
                ]),

            Step::make('Email')
                ->schema([
                    TextInput::make('email') //ke DB
                        ->label('Isi Email')
                        ->required()
                        ->email()
                        ->maxLength(255),
                ]),

        ];
    }
}
