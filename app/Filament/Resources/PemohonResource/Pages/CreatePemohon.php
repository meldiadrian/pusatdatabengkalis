<?php

namespace App\Filament\Resources\PemohonResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
////
//use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PemohonResource;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class CreatePemohon extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = PemohonResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        return $data;
    }

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


    protected function getSavedNotification(): ?Notification
    {
        return null; // matikan notif bawaan
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->body('Data telah diperbarui dan ditampilkan.')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set instansi_pemohon dari unit_kerja_id user yang login
        $data['instansi_pemohon'] = auth()->user()->unit_kerja_id;
        return $data;
    }

    protected function handleRecordCreation(array $data): \App\Models\Pemohon
    {
        // Memastikan instansi_pemohon diset sebelum create
        $data['instansi_pemohon'] = auth()->user()->unit_kerja_id;
        return parent::handleRecordCreation($data);
    }

    protected function getSteps(): array
    {
        return [
            Step::make('Instansi Pemohon')
                //->description('Pilih Instansi')
                ->schema([
                    Select::make('instansi_pemohon')
                        ->label('Pilih Instansi Pemohon')
                        ->relationship('unitKerja', 'nama_opd') //relasi/pengambilan data dari tabel unit kerja
                        ->searchable()
                        ->preload()
                        ->default(auth()->user()->unit_kerja_id)
                        ->disabled()
                        ->required(),
                ]),

            Step::make('Data Yang Dibutuhkan')
                ->schema([
                    Textarea::make('data_diminta') //ke DB
                        ->label('Data Yang Dibutuhkan')
                        ->required(),
                ]),


            Step::make('Tujuan Penggunaan Data')
                ->schema([
                    Textarea::make('tujuan_penggunaan') //ke DB
                        ->label('Tujuan Penggunaan Data')
                        ->required(),
                ]),

            Step::make('Instansi Yang Dituju')
                //->description('Pilih Instansi')
                ->schema([
                    Select::make('opd_tujuan')
                        ->label('Pilih Instansi Tujuan')
                        ->relationship('unitKerjaTujuan', 'nama_opd') //relasi/pengambilan data dari tabel unit kerja
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Step::make('Upload Dokumen')
                // ->description('Please Upload Your File')
                ->schema([
                    FileUpload::make('upload_surat') //ke DB
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.ms-excel', // xls
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
                            'application/msword', // .doc
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                            'image/jpeg', // .jpg, .jpeg
                            'image/png', // .png

                        ])
                        ->label('📎 Upload Lampiran PDF,Excel,Word,jpg,jpeg,png (Max 2MB)')
                        ->directory('Surat') // simpan di folder storage/app/public/surat
                        ->disk('public')     // pakai disk public
                        ->maxSize(2048)
                        ->required(),

                ]),


        ];
    }
}
