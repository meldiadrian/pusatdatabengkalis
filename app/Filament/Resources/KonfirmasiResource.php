<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KonfirmasiResource\Pages;
use App\Filament\Resources\KonfirmasiResource\RelationManagers;
use App\Models\Konfirmasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;




class KonfirmasiResource extends Resource
{
    protected static ?string $model = Konfirmasi::class;
    protected static ?int $navigationSort = 0;
    protected static ?string $modelLabel = 'Konfirmasi';
    protected static ?string $pluralModelLabel = 'Konfirmasi Permohonan';
    protected static ?string $navigationGroup = 'Pusat Data Kabupaten Bengkalis';


    // public static function canViewAny(): bool
    // {
    //     $user = auth()->user();

    //     return $user && in_array($user->role, ['admin', 'sekre']);
    // }



    //---------------------------badge notifikasi menu-----------------

    public static function getNavigationBadge(): ?string
    {
        //return static::getModel()::whereNull('pemohon_id')->count();
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgePollingInterval(): ?string
    {
        return '1s';
    }

    //---------------------------badge notifikasi menu hanya divalidator-----------------


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Jika sekre → hanya lihat data miliknya sendiri
        if ($user->role == 'sekre' && $user->unitKerja->tipe == 'OPD') {

            $query->whereHas('pemohon', function ($q) use ($user) {
                $q->where('instansi_pemohon', $user->unit_kerja_id);
            });
        }
        if ($user->role == 'sekre' && $user->unitKerja->tipe == 'Desa') {
            $query->whereHas('pemohon', function ($q) use ($user) {
                $q->where('instansi_pemohon', $user->unit_kerja_id);
            });
        }
        return $query;
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();
    //     $user = auth()->user();

    //     if ($user && $user->role === 'sekre') {
    //         $query->whereHas('pemohon.unitKerja', function ($q) use ($user) {
    //             $q->where('id', $user->unit_kerja)
    //                 ->where('tipe', function ($sub) use ($user) {
    //                     $sub->select('tipe')
    //                         ->from('unit_kerjas')
    //                         ->where('id', $user->unit_kerja)
    //                         ->limit(1);
    //                 });
    //         });
    //     }

    //     return $query;
    // }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table


            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->disableClick()
                    ->rowIndex(isFromZero: false),

                Tables\Columns\TextColumn::make('pemohon.unitKerja.nama_opd')
                    ->disableClick()
                    ->label('Pemohon'),

                Tables\Columns\TextColumn::make('pemohon.data_diminta')
                    ->label('Data Diminta')
                    ->disableClick()
                    ->limit(50),

                Tables\Columns\TextColumn::make('pemohon.upload_surat')
                    ->badge()
                    ->label('Surat Permohonan')
                    ->icon('heroicon-s-document-text')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat Dokumen' : '-')
                    ->url(
                        fn($record) => $record->pemohon?->upload_surat
                            ? route('download.storage.file', [
                                'path' => $record->pemohon->upload_surat
                            ])
                            : null
                    )
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'sukses' => 'success',
                        'ditolak' => 'danger',
                        default => 'primary',
                    })
                    ->action(function ($record) {
                        if ($record->status === 'pending') {
                            $record->update([
                                'status' => 'sukses', // ubah ke sukses saat diklik
                            ]);
                        }
                    })
                // Tables\Columns\TextColumn::make('status')
                //     ->badge()
                //     ->label('Status')
                //     ->disableClick()
                //     ->color(fn($state) => match ($state) {
                //         'pending' => 'warning',
                //         'sukses' => 'success',
                //         'ditolak' => 'danger',
                //         default => 'gray',
                //     }),

            ])

            ->filters([
                //
            ])
            ->actions([
                Action::make('proses')
                    ->button()
                    ->label('Proses')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => auth()->user()?->role === 'sekre' && $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'proses pengajuan data',
                        ]);

                        // optional: update juga ke pemohon
                        $record->pemohon->update([
                            'status' => 'proses pengajuan data',
                        ]);

                        Notification::make()
                            ->title('Berhasil diproses')
                            ->body('Status berubah menjadi PROSES')
                            ->success()
                            ->send();
                    }),
                Action::make('tolak')
                    ->button()
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => auth()->user()?->role === 'sekre' && $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'ditolak',
                        ]);

                        $record->pemohon->update([
                            'status' => 'ditolak',
                        ]);

                        Notification::make()
                            ->title('Permohonan ditolak')
                            ->danger()
                            ->send();
                    }),


                Tables\Actions\EditAction::make()
                    ->button()
                    ->icon('heroicon-s-pencil')
                    ->extraAttributes([
                        'style' => 'background-color: #facc15; color: black;'
                    ])
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin'])),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->extraAttributes([
                        'style' => 'background-color: #dc2626; color: white ;'
                    ])
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin'])),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKonfirmasis::route('/'),
            'create' => Pages\CreateKonfirmasi::route('/create'),
            'edit' => Pages\EditKonfirmasi::route('/{record}/edit'),
        ];
    }
}
