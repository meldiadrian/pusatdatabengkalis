<?php

namespace App\Filament\Resources\PemilikDataResource\Pages;

use App\Filament\Resources\PemilikDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class EditPemilikData extends EditRecord
{
    protected static string $resource = PemilikDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Data Pemohon')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('pemohon.unitKerja.nama_opd')
                            ->label('INSTANSI PEMOHON'),
                        TextEntry::make('pemohon.data_diminta')
                            ->label('DATA DIMINTA')
                            ->columnSpanFull(),
                        TextEntry::make('pemohon.tujuan_penggunaan')
                            ->label('TUJUAN PENGGUNAAN')
                            ->columnSpanFull(),
                        TextEntry::make('pemohon.unitKerjaTujuan.nama_opd')
                            ->label('INSTANSI TUJUAN'),
                        TextEntry::make('pemohon.created_at')
                            ->label('TANGGAL PERMOHONAN')
                            ->dateTime(),
                    ]),
                Section::make('Detail Keterangan')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                        TextEntry::make('unitKerja.nama_opd')
                            ->label('Instansi Penyedia Data'),
                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime(),
                    ]),
            ]);
    }

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
        $this->redirect($this->getResource()::getUrl('index')); //untuk memberikan redirect setelah penyimpanan
    }
}
