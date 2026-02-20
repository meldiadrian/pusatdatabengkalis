<?php

namespace App\Filament\Resources\PemilikDataResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;

class SuratBalasanRelationManager extends RelationManager
{
    protected static string $relationship = 'suratBalasan';

    protected static ?string $recordTitleAttribute = 'keterangan';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                IconColumn::make('upload_balasan')
                    ->label('SURAT BALASAN')
                    ->url(fn($record) => $record->upload_balasan ? route('download.storage.file', ['path' => 'Surat/Balasan/' . $record->upload_balasan]) : null)
                    ->openUrlInNewTab()
                    ->alignCenter()
                    ->icon('heroicon-s-arrow-down')
                    ->color('success'),

                TextColumn::make('keterangan')->label('KETERANGAN')->wrap(),
                TextColumn::make('created_at')->label('DIBUAT')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('download')
                    ->label('📥 Download')
                    ->icon('heroicon-s-arrow-down')
                    ->color('primary')
                    ->url(function ($record) {
                        if ($record->upload_balasan) {
                            return route('download.storage.file', ['path' => 'Surat/Balasan/' . $record->upload_balasan]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->disabled(fn($record) => !$record->upload_balasan),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('upload_balasan')
                    ->label('Upload Surat Balasan')
                    ->disk('public')
                    ->directory('Surat/Balasan')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(false),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->required(false),
            ]);
    }
}
