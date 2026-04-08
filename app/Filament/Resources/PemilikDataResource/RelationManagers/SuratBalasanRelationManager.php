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
use Illuminate\Database\Eloquent\Model;

class SuratBalasanRelationManager extends RelationManager
{
    protected static string $relationship = 'suratBalasan';

    protected static ?string $recordTitleAttribute = 'keterangan';


    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Data yang Diminta';
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                IconColumn::make('upload_balasan')
                    ->label('Data yang Diminta')
                    //->url(fn($record) => $record->upload_balasan ? route('download.storage.file', ['path' => 'Surat/Balasan/' . $record->upload_balasan]) : null)
                    ->url(function ($record) {
                        if (!$record->upload_balasan) {
                            return null;
                        }

                        if (!\Storage::disk('public')->exists($record->upload_balasan)) {
                            return null;
                        }

                        return route('download.storage.file', [
                            'path' => $record->upload_balasan
                        ]);
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload Data yang Diminta')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('')
                    ->color('primary'),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('download')
                    ->label('📥 Download')
                    ->icon('heroicon-s-arrow-down')
                    ->color('primary')
                    // ->url(function ($record) {
                    //     if ($record->upload_balasan) {
                    //         return route('download.storage.file', ['path' => 'Surat/Balasan/' . $record->upload_balasan]);
                    //     }
                    //     return null;
                    // })
                    ->url(function ($record) {
                        if (!$record->upload_balasan) {
                            return null;
                        }

                        if (!\Storage::disk('public')->exists($record->upload_balasan)) {
                            return null;
                        }

                        return route('download.storage.file', [
                            'path' => $record->upload_balasan
                        ]);
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
                    ->label('Upload Data yang Diminta (PDF, Excel, Word, jpg, jpeg, png - Max 4MB)')
                    ->maxSize(4096) // 4MB
                    ->disk('public')
                    ->directory('Surat/Balasan')
                    ->acceptedFileTypes(['application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png'])
                    ->required(false),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->required(false),
            ]);
    }
}
