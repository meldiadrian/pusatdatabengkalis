<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DaftarAplikasiPerangkatDaerah;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Pages;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;
use Filament\Facades\Filament;
use App\Models\User;

class DaftarAplikasiPerangkatDaerahResource extends Resource
{
    protected static ?string $model = DaftarAplikasiPerangkatDaerah::class;
    protected static ?string $modelLabel = 'Daftar Aplikasi Perangkat Daerah';
    protected static ?string $pluralModelLabel = 'Daftar Aplikasi Perangkat Daerah';
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?string $navigationGroup = 'Daftar Aplikasi & Website';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('unitKerja', function ($q) {
                $q->where('tipe', 'OPD');
            });
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('unit_kerja_id', fn($query) => $query->where('tipe', 'opd'))
                //     ->relationship('unitKerja', 'nama_opd')
                //     ->label('Perangkat Daerah')
                //     // ->default(fn() => auth()->user()->unit_kerja_id)
                //     //->disabled()
                //     ->dehydrated()
                //     ->placeholder('Pilih perangkat daerah')
                //     ->required(),

                Forms\Components\Select::make('unit_kerja_id')
                    ->relationship(
                        name: 'unitKerja',
                        titleAttribute: 'nama_opd',
                        modifyQueryUsing: fn($query) => $query->where('tipe', 'opd')
                    )
                    ->label('Perangkat Daerah')
                    ->default(fn() => auth()->user()->unit_kerja_id)
                    // ->default(fn() => auth()->user()->unit_kerja_id)
                    ->placeholder('Pilih perangkat daerah')
                    //->disabled()
                    ->required(),

                Forms\Components\TextInput::make('websiteopd')
                    ->label('Domain Website Kantor')
                    ->placeholder('Ex. www.bengkaliskab.go.id, Isi Dengan Tanda (-) Jika tidak ada')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama_aplikasi')
                    ->label('Nama Aplikasi')
                    ->placeholder('Mohon Isi Nama Aplikasi')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Mohon diisi Nama Aplikasi',
                    ]),

                Forms\Components\Select::make('jenis_aplikasi')
                    ->label('Jenis Aplikasi')
                    ->placeholder('Pilih Jenis Aplikasi')
                    ->required()
                    ->options([
                        'web' => 'Web',
                        'mobile' => 'Mobile',
                    ]),

                Forms\Components\TextInput::make('alamat_domain')
                    ->label('Alamat Domain')
                    ->required()
                    ->placeholder('Mohon Isi Alamat Domain')
                    ->maxLength(255),

                Forms\Components\Textarea::make('spesifikasi_teknis')
                    ->label('Spesifikasi Teknis')
                    ->placeholder('Database mysql, bahasa program laravel, android (kotlin)')
                    ->required()
                    ->maxLength(255),

                Select::make('mode')
                    ->label('Sifat')
                    ->placeholder('Pilih Sifat Aplikasi Online/Offline')
                    ->options(array_combine(
                        DaftarAplikasiPerangkatDaerah::MODE,
                        DaftarAplikasiPerangkatDaerah::MODE
                    ))
                    ->required(),

                Forms\Components\TextInput::make('tahun_penganggaran')
                    ->label('Tahun Pembuatan')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(2000)
                    ->maxValue(now()->year + 5)
                    ->maxLength(4)
                    ->placeholder('2026'),

                Forms\Components\Select::make('pembuat')
                    ->label('Pembuat')
                    // ->placeholder('Pilih Pembuat Pihak Ketiga/Diskominfotik')
                    ->required()
                    ->options([
                        'internal' => 'Internal',
                        'pihak ketiga' => 'Pihak Ketiga',
                        'diskominfotik' => 'Diskominfotik',
                    ]),

                Forms\Components\Select::make('pemilik_aplikasi')
                    ->label('Pemilik Aplikasi')
                    ->placeholder('Pilih Pemilik Aplikasi')
                    ->required()
                    ->options([
                        'pusat' => 'Pusat',
                        'daerah' => 'Daerah',
                    ]),

                Select::make('status')
                    ->label('Status')
                    ->placeholder('Pilih Status Aplikasi Aktif/Tidak Aktif')
                    ->options(array_combine(
                        DaftarAplikasiPerangkatDaerah::STATUS,
                        DaftarAplikasiPerangkatDaerah::STATUS
                    ))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('dimanfaatkan_untuk_layanan')
                    ->label('Dimanfaatkan Untuk Layanan')
                    ->placeholder(' Mohon Isi dimanfaatkan Untuk Layanan Apa')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Mohon Isi keterangan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {

        $user = auth()->user();
        $isBasicUser = $user->role === 'user';
        $isBasicAdmin = $user->role === 'admin';
        return $table
            ->emptyStateHeading('Belum ada data')
            ->emptyStateDescription('Silakan tambahkan data baru.')
            ->emptyStateIcon('heroicon-o-user')
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(isFromZero: false),

                //--------------------vew user----------------------------//
                TextColumn::make('unitKerja.nama_opd')
                    ->disableClick()
                    ->label('Nama Perangkat Daerah')
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(
                        query: function (Builder $query, string $search) {
                            $query->orWhereHas('unitKerja', function (Builder $q) use ($search) {
                                $q->where('nama_opd', 'like', "%{$search}%");
                            });
                        }
                    ),


                // TextColumn::make('unitKerja.alamat')
                //     ->disableClick()
                //     ->label('Alamat Kantor')
                //     ->searchable(
                //         query: function (Builder $query, string $search) {
                //             $query->orWhereHas('unitKerja', function (Builder $q) use ($search) {
                //                 $q->where('alamat', 'like', "%{$search}%");
                //             });
                //         }
                //     ),
                Tables\Columns\TextColumn::make('nama_aplikasi')
                    ->disableClick()
                    ->label('Nama Aplikasi')
                    ->wrap()
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),

                Tables\Columns\TextColumn::make('mode')
                    ->disableClick()
                    ->label('Status')
                    ->wrap()
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),

                Tables\Columns\TextColumn::make('alamat_domain')
                    ->label('Alamat Domain')
                    ->disableClick()
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->wrap()
                    ->copyable()
                    ->limit(80)
                    ->searchable()
                    ->extraAttributes(fn($record) => [
                        'style' => 'cursor:pointer; color:#2563eb;',
                        'onclick' => "window.open('" .
                            (str_starts_with($record->alamat_domain, 'http')
                                ? $record->alamat_domain
                                : 'https://' . $record->alamat_domain) .
                            "', '_blank', 'width=900,height=600,resizable=yes,scrollbars=yes')",
                    ]),

                Tables\Columns\TextColumn::make('tahun_penganggaran')
                    ->disableClick()
                    ->label('Tahun Penganggaran')
                    ->wrap()
                    // ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),

                Tables\Columns\TextColumn::make('dimanfaatkan_untuk_layanan')
                    ->disableClick()
                    ->label('Dimanfaatkan Untuk Layanan')
                    ->wrap()
                    // ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),

                Tables\Columns\TextColumn::make('pembuat')
                    ->label('Pembuat')
                    ->disableClick()
                    ->wrap()
                    ->limit(80)
                    ->visible(!$isBasicUser)
                    ->searchable(),

                Tables\Columns\TextColumn::make('spesifikasi_teknis')
                    ->label('Spesifikasi Teknis')
                    ->disableClick()
                    ->wrap()
                    ->limit(25)
                    ->visible(!$isBasicUser)
                    ->searchable(),

                Tables\Columns\TextColumn::make('jenis_aplikasi')
                    ->disableClick()
                    ->label('Jenis Aplikasi')
                    ->wrap()
                    ->limit(80)
                    // ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),
                //------------------------end off vie user---------------------//

                //--------------view admin----------------------//   



                Tables\Columns\TextColumn::make('status')
                    ->disableClick()
                    ->label('Status')
                    ->wrap()
                    ->limit(80)
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->disableClick()
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(80)
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),
                //----------end view admin----------------//

            ])
            ->filters([
                //
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->icon('heroicon-s-pencil')
                    // ->extraAttributes([
                    //     'style' => 'background-color: #facc15; color: black;'
                    // ])
                    // ->visible(fn() => auth()->user()?->unitKerja?->tipe === 'OPD'),
                    ->extraAttributes([
                        'style' => 'background-color: #facc15; color: black;'
                    ])
                    // ->visible(fn($record) => $record->user_id === auth()->id()),
                    ->visible(
                        fn($record) =>
                        strtolower(auth()->user()->role) === 'admin'
                        || $record->user_id == auth()->id()
                    ),


                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->extraAttributes([
                        'style' => 'background-color: danger; color: white;'
                    ])
                    //->visible(fn($record) => $record->user_id === auth()->id())
                    ->visible(
                        fn($record) =>
                        strtolower(auth()->user()->role) === 'admin'
                        || $record->user_id == auth()->id()
                    ),
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
            'index' => Pages\ListDaftarAplikasiPerangkatDaerahs::route('/'),
            'create' => Pages\CreateDaftarAplikasiPerangkatDaerah::route('/create'),
            'edit' => Pages\EditDaftarAplikasiPerangkatDaerah::route('/{record}/edit'),
        ];
    }
}
