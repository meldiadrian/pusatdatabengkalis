<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use App\Models\WebsitePerangkatDaerah;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DaftarWebsitePerangkatDaerah;
use App\Models\DaftarAplikasiPerangkatDaerah;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\Pages;
use App\Filament\Resources\DaftarWebsitePerangkatDaerahResource\RelationManagers;

class DaftarWebsitePerangkatDaerahResource extends Resource
{
    // protected static ?string $model = DaftarWebsitePerangkatDaerah::class;
    protected static ?string $model = WebsitePerangkatDaerah::class;
    protected static ?string $modelLabel = 'Daftar Website Perangkat Daerah';
    protected static ?string $pluralModelLabel = 'Daftar Website Perangkat Daerah';
    protected static ?string $navigationGroup = 'Daftar Aplikasi & Website';
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_kerja_id')
                    ->relationship('unitKerja', 'nama_opd')
                    ->label('Perangkat Daerah')
                    ->default(fn() => auth()->user()->unit_kerja_id)
                    //->disabled()
                    ->dehydrated()
                    ->placeholder('Pilih perangkat daerah')
                    ->required(),

                Forms\Components\TextInput::make('alamat_domain')
                    ->label('Alamat Website')
                    ->required()
                    ->placeholder('Isi alamat domain ex. www.google.com')
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

                Tables\Columns\TextColumn::make('alamat_domain')
                    ->label('Alamat Website')
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

                Tables\Columns\TextColumn::make('pembuat')
                    ->label('Pembuat')
                    ->disableClick()
                    ->wrap()
                    ->limit(80)
                    ->visible(! $isBasicUser)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->disableClick()
                    ->wrap()
                    ->limit(80)
                    ->visible(! $isBasicUser)
                    ->searchable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->disableClick()
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(80)
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user']))
                    ->searchable(),

            ])
            ->filters([
                //
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user'])),
                Tables\Actions\DeleteAction::make()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDaftarWebsitePerangkatDaerahs::route('/'),
            'create' => Pages\CreateDaftarWebsitePerangkatDaerah::route('/create'),
            'edit' => Pages\EditDaftarWebsitePerangkatDaerah::route('/{record}/edit'),
        ];
    }
}
