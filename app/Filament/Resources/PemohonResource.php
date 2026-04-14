<?php

namespace App\Filament\Resources;

use filament;
use Filament\Forms;
use App\Models\Pemohon;
use Filament\Forms\Form;
use App\Models\SuratBalasan;
use App\Exports\PemohonExport;
use Filament\Resources\Resource;
use App\Exports\PemohonPdfExport;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Facades\FilamentIcon;
use App\Filament\Resources\PemohonResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PemohonResource\RelationManagers;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class PemohonResource extends Resource
{
    protected static ?string $model = Pemohon::class;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';
    protected static ?string $modelLabel = 'Instansi Pemohon';
    protected static ?string $pluralModelLabel = 'Pemohon';
    protected static ?string $navigationGroup = 'Permintaan Data';
    protected static ?int $navigationSort = 0;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('instansi_pemohon')
                    ->relationship('unitKerja', 'nama_opd') //relasi/pengambilan data dari tabel unit kerja
                    ->label('Instansi Pemohon')
                    ->required()
                    ->validationMessages([
                        'required' => 'Mohon jelaskan data yang dibutuhkan',
                    ]),

                Forms\Components\Textarea::make('data_diminta')
                    ->label('Data Yang Dibutuhkan')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('tujuan_penggunaan')
                    ->label('Tujuan Penggunaan Data')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('opd_tujuan')
                    ->relationship('unitKerja', 'nama_opd') //relasi/pengambilan data dari tabel unit kerja
                    ->label('Instansi Tujuan')
                    ->required(),
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
                    ->label('📎 Upload Lampiran PDF/Excel')
                    ->maxSize(2048) // 2MB
                    ->directory('Surat') // simpan di folder storage/app/public/surat
                    ->disk('public')     // pakai disk public
                    ->visibility('public')
                    ->required(),
            ]);
    }

    //Isi tampilan
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                auth()->user()->isAdmin()
                    ? $query
                    : $query->whereHas('unitKerja', function ($q) {
                        $q->where('unit_kerjas.id', auth()->user()->unit_kerja_id);
                    })
            )
            ->columns([

                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(isFromZero: false),

                Tables\Columns\TextColumn::make('unitKerja.nama_opd') //relasi ke DB unit kerja
                    ->label('Pemohon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unitKerjaTujuan.nama_opd') //relasi ke DB unit kerja
                    ->label('Penyedia Data')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_diminta')
                    ->label('Data Request')
                    ->wrap() //fungsi teks jadi rata kebawah
                    ->limit(80) //limit teks
                    ->searchable(),
                Tables\Columns\TextColumn::make('tujuan_penggunaan')
                    ->label('Pemanfaatan Data')
                    ->wrap() //fungsi teks jadi rata kebawah
                    ->limit(80) //limit teks
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'sukses' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('upload_surat')
                    ->label('Surat Permohonan')
                    ->hidden(),

                // IconColumn::make('surat_balasan')
                //     ->label('SURAT BALASAN')
                //     ->tooltip('Download') //teks link download saat kursos diarahkan
                //     ->url(fn($record) => asset('storage/' . $record->surat_balasan))
                //     ->openUrlInNewTab()
                //     ->alignCenter()
                //     ->icon('heroicon-o-circle-stack') // solid download icon
                //     ->color('success'),
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
            ->filters([
                //
            ])
            ->actions([
                Action::make('downloadSurat')
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
                        return $last && $last->upload_balasan ? route('download.storage.file', ['path' => $last->upload_balasan]) : null;
                    })
                    ->openUrlInNewTab()
                    ->hidden(fn($record) => !$record->suratBalasan()->latest()->first() || !$record->suratBalasan()->latest()->first()->upload_balasan),

                Tables\Actions\EditAction::make()
                    ->button()
                    ->icon('heroicon-s-pencil')
                    ->extraAttributes([
                        'style' => 'background-color: #facc15; color: black;'
                    ])
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user'])),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListPemohons::route('/'),
            'create' => Pages\CreatePemohon::route('/create'),
            'edit' => Pages\EditPemohon::route('/{record}/edit'),
        ];
    }
}
