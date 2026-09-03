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
    protected static ?string $navigationGroup = 'e-Data Kabupaten Bengkalis';


    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();
    //     $user = auth()->user();

    //     // FILTER: hanya tampilkan yang pending
    //     $query->where('status', 'pending');

    //     // ✅ Admin: lihat semua data pending
    //     if ($user->role === 'admin') {
    //         return $query;
    //     }

    //     // ❌ Jika bukan sekre → tidak boleh lihat
    //     if ($user->role !== 'sekre') {
    //         return $query->whereRaw('1 = 0');
    //     }

    //     // ❌ Jika sekre tapi tipe tidak sesuai
    //     if (!in_array(optional($user->unitKerja)->tipe, ['OPD', 'Desa'])) {
    //         return $query->whereRaw('1 = 0');
    //     }

    //     // ✅ Sekre valid → filter berdasarkan instansi
    //     return $query->whereHas('pemohon', function ($q) use ($user) {
    //         $q->where('instansi_pemohon', $user->unit_kerja_id);
    //     });
    // }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        $isAdmin = $user->role === 'admin';
        $isSekre = $user->role === 'sekre';
        $isValidTipe = in_array(optional($user->unitKerja)->tipe, ['OPD', 'Desa']);

        return $isAdmin || ($isSekre && $isValidTipe);
    }

    //---------------------------badge notifikasi menu-----------------


    // public static function getNavigationBadge(): ?string
    // {
    //     //return static::getModel()::where('status', 'pending')->count();

    //     $user = auth()->user();

    //     $query = static::getModel()::query()
    //         ->where('status', 'pending');

    //     // Filter berdasarkan role sekre
    //     if ($user->role == 'sekre' && $user->unitKerja->tipe == 'OPD') {
    //         $query->whereHas('pemohon', function ($q) use ($user) {
    //             $q->where('instansi_pemohon', $user->unit_kerja_id);
    //         });
    //     }

    //     if ($user->role == 'sekre' && $user->unitKerja->tipe == 'Desa') {
    //         $query->whereHas('pemohon', function ($q) use ($user) {
    //             $q->where('instansi_pemohon', $user->unit_kerja_id);
    //         });
    //     }

    //     return (string) $query->count();
    // }

    // public static function getNavigationBadge(): ?string
    // {
    //     $user = auth()->user();

    //     $isAdmin = $user->role === 'admin';
    //     $isSekre = $user->role === 'sekre';
    //     $isValidTipe = in_array(optional($user->unitKerja)->tipe, ['OPD', 'Desa']);

    //     // ❌ Selain admin & sekre valid → tidak tampil badge
    //     if (! $isAdmin && ! ($isSekre && $isValidTipe)) {
    //         return null;
    //     }

    //     $query = static::getModel()::query()
    //         ->where('status', 'pending');

    //     // ✅ Jika sekre → filter berdasarkan instansi
    //     if ($isSekre && $isValidTipe) {
    //         $query->whereHas('pemohon', function ($q) use ($user) {
    //             $q->where('instansi_pemohon', $user->unit_kerja_id);
    //         });
    //     }

    //     // ✅ Admin → langsung count semua pending
    //     return (string) $query->count();
    // }

    // public static function getNavigationBadgeColor(): ?string
    // {
    //     return 'danger';
    // }


    // public static function getNavigationBadgePollingInterval(): ?string
    // {
    //     return '1s';
    // }


    //---------------------------badge notifikasi menu hanya divalidator-----------------

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
            ->emptyStateHeading('Belum ada data')
            ->emptyStateDescription('Belum ada data permintaan konfirmasi.')
            ->emptyStateIcon('heroicon-o-clipboard-document')


            // ->modifyQueryUsing(
            //     fn(Builder $query) =>
            //     auth()->user()->role === 'admin'
            //         ? $query
            //         : $query->whereHas('pemohon', function (Builder $q) {
            //             $q->where('instansi_pemohon', auth()->user()->unit_kerja_id);
            //         })
            // )

            ->modifyQueryUsing(function (Builder $query) {

                // tampilkan hanya status pending
                $query->where('status', 'pending');

                // jika admin tampil semua pending
                if (auth()->user()->role === 'admin') {
                    return $query;
                }

                // selain admin filter berdasarkan instansi
                return $query->whereHas('pemohon', function (Builder $q) {
                    $q->where('instansi_pemohon', auth()->user()->unit_kerja_id);
                });
            })

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
                                'status' => 'sukses',
                            ]);

                            // update juga tabel pemohon menjadi sukses
                            $record->pemohon->update([
                                'status' => 'sukses',
                            ]);

                            Notification::make()
                                ->title('Status berhasil diubah')
                                ->body('Status berubah menjadi SUKSES')
                                ->success()
                                ->send();
                        }
                    })
            ])
            ->poll('1s')

            ->filters([
                //
            ])
            ->actions([
                // Action::make('proses')
                //     ->button()
                //     ->label('Proses')
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
                //     ->visible(fn($record) => auth()->user()?->role === 'sekre' && $record->status === 'pending')
                //     // ->action(function ($record) {
                //     //     $record->update([
                //     //         'status' => 'proses pengajuan data',
                //     //     ]);

                //     //     // optional: update juga ke pemohon
                //     //     $record->pemohon->update([
                //     //         'status' => 'proses pengajuan data',
                //     //     ]);

                //     ->action(function ($record) {

                //         $record->update([
                //             'status' => 'proses',
                //         ]);

                //         // update juga tabel pemohon
                //         $record->pemohon->update([
                //             'status' => 'proses',
                //         ]);



                //         Notification::make()
                //             ->title('Berhasil diproses')
                //             ->body('Status berubah menjadi PROSES')
                //             ->success()
                //             ->send();
                //     }),

                Action::make('proses')
                    ->button()
                    ->label('Proses')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        auth()->user()?->role === 'sekre'
                        && $record->status === 'pending'
                    )
                    ->action(function ($record) {

                        $record->update([
                            'status' => 'proses',
                        ]);

                        $record->pemohon->update([
                            'status' => 'proses',
                        ]);

                        Notification::make()
                            ->title('Berhasil diproses')
                            ->body('Status berubah menjadi PROSES')
                            ->success()
                            ->send();
                    })
                    ->after(function ($livewire) {
                        $livewire->dispatch('$refresh');
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
