<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pemohon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PemilikData;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\FileColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PemilikDataResource\Pages;
use App\Filament\Resources\PemilikDataResource\RelationManagers;


class PemilikDataResource extends Resource
{

    // protected static ?string $model = PemilikData::class;
    protected static ?string $model = Pemohon::class;
    protected static ?string $navigationIcon = 'heroicon-s-book-open';
    protected static ?string $modelLabel = 'Instansi Penyedia Data';
    protected static ?string $pluralModelLabel = 'Pemilik Data';
    protected static ?string $navigationGroup = 'Pusat Data Kabupaten Bengkalis';
    protected static ?int $navigationSort = 0;


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
    //-------------------------------

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if (!$user || !$user->unitKerja) {
            return null;
        }

        // Hide badge untuk pemohon
        if ($user->role === 'pemohon') {
            return null;
        }

        // ROLE SEKRE - hanya untuk OPD dan Desa
        if ($user->role === 'sekre') {
            if (in_array($user->unitKerja->tipe, ['OPD', 'Desa'])) {
                $count = static::getModel()::query()
                    ->where('status', 'pending')
                    ->where('instansi_pemohon', $user->unit_kerja_id)
                    ->count();

                return $count > 0 ? (string) $count : null;
            }
            return null;
        }

        // OPD TUJUAN - untuk admin_opd, user, dan role apapun dengan tipe OPD/Desa
        if (
            $user->role === 'admin_opd' ||
            $user->role === 'user' ||
            in_array($user->unitKerja->tipe, ['OPD', 'Desa'])
        ) {
            $count = static::getModel()::query()
                ->whereIn('status', ['pending', 'proses'])
                ->where('opd_tujuan', $user->unit_kerja_id)
                ->count();

            return $count > 0 ? (string) $count : null;
        }

        return null;
    }

    //------------------------------

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgePollingInterval(): ?string
    {
        return '1s';
    }

    //---------------------------badge notifikasi menu hanya divalidator-----------------


    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()?->email !== 'sekre@admin.com';

    // }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        $isAdmin = $user->role === 'admin';
        $isUser = $user->role === 'user';
        $isValidTipe = in_array(optional($user->unitKerja)->tipe, ['OPD', 'Desa']);

        return $isAdmin || ($isUser && $isValidTipe);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Instansi Pemohon')
                    ->description('Data instansi yang mengajukan permohonan data')
                    ->schema([
                        Forms\Components\Select::make('instansi_pemohon')
                            ->relationship('unitKerja', 'nama_opd')
                            ->label('Instansi Pemohon')
                            ->required()
                            ->searchable()
                            ->disabled()
                            ->preload(),
                        Forms\Components\Textarea::make('data_diminta')
                            ->label('Data Yang Dibutuhkan')
                            ->required()
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tujuan_penggunaan')
                            ->label('Tujuan Penggunaan Data')
                            ->required()
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('opd_tujuan')
                            ->relationship('unitKerja', 'nama_opd')
                            ->label('Instansi Tujuan')
                            ->required()
                            ->disabled()
                            ->searchable()
                            ->preload(),
                        Forms\Components\FileUpload::make('upload_surat')
                            ->label('📎 Upload Surat Permohonan')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'image/jpeg',
                                'image/png',
                            ])
                            ->directory('Surat')
                            ->maxSize(4096) // 4MB
                            ->disk('public')
                            ->required()
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                auth()->user()->isAdmin()
                    ? $query
                    : $query->where('opd_tujuan', auth()->user()->unit_kerja_id)
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(isFromZero: false),

                TextColumn::make('unitKerja.nama_opd')
                    ->label('Pemohon')
                    ->searchable(
                        query: function (Builder $query, string $search) {
                            $query->orWhereHas('unitKerja', function (Builder $q) use ($search) {
                                $q->where('nama_opd', 'like', "%{$search}%");
                            });
                        }
                    ),
                TextColumn::make('unitKerjaTujuan.nama_opd')
                    ->label('Penyedia Data')
                    ->searchable(
                        query: function (Builder $query, string $search) {
                            $query->orWhereHas('unitKerjaTujuan', function (Builder $q) use ($search) {
                                $q->where('nama_opd', 'like', "%{$search}%");
                            });
                        }
                    ),

                Tables\Columns\TextColumn::make('data_diminta')
                    ->label('Data Request')
                    ->wrap()
                    ->limit(80)
                    ->searchable(),

                Tables\Columns\TextColumn::make('tujuan_penggunaan')
                    ->label('Pemanfaatan Data')
                    ->wrap()
                    ->limit(80)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'sukses' => 'success',
                        'ditolak' => 'danger',
                        default => 'primary',
                    }),


                Tables\Columns\TextColumn::make('keterangan_surat_balasan')
                    ->label('Keterangan')
                    ->getStateUsing(function ($record) {
                        $last = $record->suratBalasan()->latest()->first();
                        return $last?->keterangan ?? '';
                    })
                    ->wrap()
                    ->limit(80),


                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->poll('1s')
            ->filters([
                //
            ])
            ->actions([
                Action::make('downloadSurat')
                    // ->label(fn() => new HtmlString('<div class="text-center leading-tight">
                    //                                     <span class="text-xs text-warning-500">Surat</span><br>
                    //                                     <span class="font-semibold">Permohonan</span>
                    //                                 </div>'))
                    ->label('Surat Permohonan')
                    ->tooltip('Download')
                    ->icon('heroicon-s-circle-stack')
                    ->color('warning')
                    ->url(function ($record) {
                        return $record->upload_surat ? route('download.storage.file', ['path' => $record->upload_surat]) : null;
                    })
                    ->openUrlInNewTab()
                    ->hidden(fn($record) => !$record->upload_surat),

                Action::make('downloadBalasan')
                    ->label('Surat Balasan')
                    ->tooltip('Download')
                    ->icon('heroicon-m-circle-stack')
                    ->color('success')
                    ->url(function ($record) {
                        $last = $record->suratBalasan()->latest()->first();
                        if ($last && $last->upload_balasan) {
                            return route('download.storage.file', ['path' => $last->upload_balasan]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->hidden(fn($record) => !$record->suratBalasan()->latest()->first() || !$record->suratBalasan()->latest()->first()->upload_balasan),


                Tables\Actions\EditAction::make()
                    ->label('Proses')
                    ->button()
                    ->icon('heroicon-s-pencil')
                    ->extraAttributes([
                        'style' => 'background-color: #facc15; color: black;'
                    ])
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user'])),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->extraAttributes([
                        'style' => 'background-color: #dc2626; color: white ;'
                    ])
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user'])),
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
            RelationManagers\SuratBalasanRelationManager::class,
            RelationManagers\PemilikDataRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemilikData::route('/'),
            // 'create' => Pages\CreatePemilikData::route('/create'),
            'edit' => Pages\EditPemilikData::route('/{record}/edit'),
        ];
    }
}
