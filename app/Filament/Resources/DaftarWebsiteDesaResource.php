<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Desa;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Kecamatan;
use App\Models\UnitKerja;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use App\Models\DaftarWebsiteDesa;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use App\Models\DaftarAplikasiPerangkatDaerah;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DaftarWebsiteDesaResource\Pages;
use App\Filament\Resources\DaftarWebsiteDesaResource\RelationManagers;




class DaftarWebsiteDesaResource extends Resource
{
    //protected static ?string $model = Kecamatan::class;
    protected static ?string $model = DaftarWebsiteDesa::class;
    protected static ?string $modelLabel = 'Daftar Website Desa';
    protected static ?string $pluralModelLabel = 'Daftar Website Desa';
    protected static ?string $navigationIcon = 'heroicon-s-chart-bar-square';
    protected static ?string $navigationGroup = 'Daftar Aplikasi & Website';
    protected static ?int $navigationSort = 3;


    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->with('kecamatan');
    // }
    public static function canViewAny(): bool
    {
        return auth()->check(); // semua role boleh lihat tabel
    }


    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->whereHas('unitKerja', function ($q) {
    //             $q->where('tipe', 'OPD');
    //         });
    // }

    // public function unitKerja()
    // {
    //     return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    // }

    // public static function getEloquentQuery(): Builder
    // {
    //     $user = auth()->user();

    //     return parent::getEloquentQuery()
    //         ->when($user->role === 'user', function ($query) use ($user) {
    //             $query->where('id', $user->id);
    //         })
    //         ->with('kecamatan', 'UnitKerja');
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\Hidden::make('id')
                    ->default(auth()->id()),

                Forms\Components\Select::make('kecamatan_id')
                    ->label('Kecamatan')
                    ->options(
                        Kecamatan::orderBy('nama_kecamatan', 'asc')
                            ->pluck('nama_kecamatan', 'id')
                    )
                    ->searchable()
                    ->reactive()
                    ->placeholder('Pilih Kecamatan')
                    ->required()
                    ->afterStateUpdated(fn(callable $set) => $set('desa_ids', [])),

                CheckboxList::make('desa_ids')
                    ->label('Desa')
                    ->options(
                        fn(callable $get) =>
                        Desa::where('kecamatan_id', $get('kecamatan_id'))
                            ->pluck('nama_desa', 'id')
                            ->toArray()
                    )
                    ->columns(2)
                    ->required()
                    ->reactive(),

                Forms\Components\TextInput::make('websitedesa')
                    ->label('Alamat Website')
                    ->required()
                    ->placeholder('Isi alamat website ex. www.google.com')
                    ->maxLength(255),

                Forms\Components\Select::make('pembuat')
                    ->label('Pembuat')
                    ->placeholder('Pilih pembuat pihak ketiga/diskominfotik')
                    ->required()
                    ->options([
                        'pihak ketiga' => 'Pihak Ketiga',
                        'diskominfotik' => 'Diskominfotik',
                    ]),

                Select::make('status')
                    ->label('Status')
                    ->placeholder('Pilih status aplikasi aktif/tidak aktif')
                    ->options(array_combine(
                        DaftarAplikasiPerangkatDaerah::STATUS,
                        DaftarAplikasiPerangkatDaerah::STATUS
                    ))
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Isi keterangan')
                    ->required()
                    ->maxLength(255),


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
                    ->disableClick()
                    ->rowIndex(isFromZero: false),

                TextColumn::make('kecamatan.nama_kecamatan')
                    ->label('Kecamatan')
                    ->disableClick()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('desa_ids')
                    ->label('Nama Desa')
                    ->disableClick()
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '-';
                        }

                        $ids = is_array($state) ? $state : [$state];

                        return \App\Models\Desa::whereIn('id', $ids)
                            ->pluck('nama_desa')
                            ->implode(', ');
                    })
                    ->wrap(),

                TextColumn::make('websitedesa')
                    ->label('Alamat Website')
                    ->disableClick()
                    ->wrap()
                    ->copyable()
                    ->limit(80)
                    ->searchable()
                    ->extraAttributes(fn($record) => [
                        'style' => 'cursor:pointer; color:#2563eb;',
                        'onclick' => "window.open('" .
                            (str_starts_with($record->website, 'http')
                                ? $record->website
                                : 'https://' . $record->website) .
                            "', '_blank', 'width=900,height=600,resizable=yes,scrollbars=yes')",
                    ]),

                TextColumn::make('pembuat')
                    ->disableClick()
                    ->label('Pembuat')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                TextColumn::make('status')
                    ->disableClick()
                    ->label('Status')
                    ->searchable(),

                TextColumn::make('keterangan')
                    ->disableClick()
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

            ])
            ->filters([
                //
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->icon('heroicon-s-pencil')
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
                        'style' => 'background-color: #dc2626; color: white;'
                    ])
                    // ->visible(
                    //     fn($record) =>
                    //     auth()->user()->role === 'admin' ||

                    //         $record->user_id === auth()->id()
                    // )

                    ->visible(
                        fn($record) =>
                        auth()->user()->role === 'admin'
                        || $record->user_id === auth()->id()
                    )
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
            'index' => Pages\ListDaftarWebsiteDesas::route('/'),
            'create' => Pages\CreateDaftarWebsiteDesa::route('/create'),
            'edit' => Pages\EditDaftarWebsiteDesa::route('/{record}/edit'),
        ];
    }
}
