<?php

namespace App\Filament\Resources\PemilikDataResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class PemilikDataRelationManager extends RelationManager
{
    protected static string $relationship = 'pemilikData';

    protected static ?string $recordTitleAttribute = 'keterangan';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('unitKerja.nama_opd')
                    ->label('INSTANSI PENYEDIA DATA')
                    ->searchable(),

                TextColumn::make('keterangan')
                    ->label('KETERANGAN')
                    ->wrap()
                    ->limit(80)
                    ->searchable(),

                IconColumn::make('upload_surat')
                    ->label('SURAT PERMOHONAN')
                    ->tooltip('Download')
                    ->url(fn($record) => $record->upload_surat ? route('download.storage.file', ['path' => $record->upload_surat]) : null)
                    ->openUrlInNewTab()
                    ->alignCenter()
                    ->icon('heroicon-s-arrow-down')
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('DIBUAT')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only
            ])
            ->actions([
                // Read-only
            ])
            ->bulkActions([
                //
            ]);
    }
}
