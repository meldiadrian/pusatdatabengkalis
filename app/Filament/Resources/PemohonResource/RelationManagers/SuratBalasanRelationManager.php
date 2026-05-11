<?php

namespace App\Filament\Resources\PemohonResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction;


class SuratBalasanRelationManager extends RelationManager
{


    // protected static ?string $title = 'Surat Balasan';
    protected static string $relationship = 'suratBalasan';

    protected static ?string $recordTitleAttribute = 'keterangan';


    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Surat Balasan')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalCancelActionLabel('Batal')
                ->after(function ($record) {
                    // update status pemohon jadi disetujui
                    $this->ownerRecord->update([
                        'status' => 'disetujui'
                    ]);
                    // 🔥 TAMBAHAN: update konfirmasi juga
                    $this->ownerRecord->konfirmasi()?->update([
                        'status' => 'sukses'
                    ]);
                }),

        ];
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                IconColumn::make('upload_balasan')
                    ->label('SURAT BALASAN')
                    ->url(function ($record) {
                        if ($record->upload_balasan) {
                            $fullPath = 'Surat/Balasan/' . $record->upload_balasan;
                            return route('download.storage.file', ['path' => $fullPath]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->alignCenter()
                    ->icon('heroicon-s-circle-stack')
                    ->color('success'),

                TextColumn::make('keterangan')->label('KETERANGAN')->wrap(),
                TextColumn::make('created_at')->label('DIBUAT')->dateTime(),
            ])

            ->filters([
                //
            ])


            ->actions([
                // Read-only: no edit/delete actions



            ])
            ->bulkActions([
                // Read-only: no bulk actions
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('upload_balasan')
                    ->label('Upload data Pdf,Excel,word,jpg,jpeg,png')
                    ->maxSize(4096) // 4MB
                    ->directory('Surat/Balasan')
                    ->disk('public')
                    // ->acceptedFileTypes(['application/pdf'])
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/vnd.ms-excel', // .xls
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                        'image/jpeg', // .jpg, .jpeg
                        'image/png', // .png

                    ])
                    ->required(false),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->required(false),
            ]);
    }
}

  // protected function getTableHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make()
    //             ->label('Tambah Surat Balasan')
    //             ->icon('heroicon-o-plus')
    //             ->color('primary'),
    //     ];
    // }