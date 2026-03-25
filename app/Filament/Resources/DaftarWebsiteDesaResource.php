<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\DaftarWebsiteDesa;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DaftarAplikasiPerangkatDaerah;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DaftarWebsiteDesaResource\Pages;
use App\Filament\Resources\DaftarWebsiteDesaResource\RelationManagers;

class DaftarWebsiteDesaResource extends Resource
{
    protected static ?string $model = DaftarWebsiteDesa::class;
    protected static ?string $modelLabel = 'Daftar Website Desa';
    protected static ?string $pluralModelLabel = 'Daftar Website Desa';
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?string $navigationGroup = 'Daftar Aplikasi & Website';
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
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
