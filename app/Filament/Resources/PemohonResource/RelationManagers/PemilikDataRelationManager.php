<?php

namespace App\Filament\Resources\PemohonResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;


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
                    ->url(function ($record) {
                        if ($record->upload_surat) {
                            return route('download.storage.file', ['path' => $record->upload_surat]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->alignCenter()
                    ->icon('heroicon-s-circle-stack')
                    ->color('success'),

                IconColumn::make('surat_balasan')
                    ->label('SURAT BALASAN')
                    ->tooltip('Download')
                    ->url(function ($record) {
                        $last = $record->suratBalasan()->latest()->first();
                        if ($last && $last->upload_balasan) {
                            return route('download.storage.file', ['path' => $last->upload_balasan]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->alignCenter()
                    ->icon('heroicon-s-circle-stack')
                    ->color('primary'),

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
