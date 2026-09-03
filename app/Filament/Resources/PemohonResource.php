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
    protected static ?string $navigationIcon = 'heroicon-s-user-circle';
    protected static ?string $modelLabel = 'Instansi Pemohon';
    protected static ?string $pluralModelLabel = 'Pemohon';
    protected static ?string $navigationGroup = 'e-Data Kabupaten Bengkalis';
    protected static ?int $navigationSort = 0;



    // public static function shouldRegisterNavigation(): bool
    // {
    //     return true;
    // }


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
    // mematikan tombol tambah data pada role admin
    public static function canCreate(): bool
    {
        return auth()->user()?->role !== 'admin';
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
                    ->validationMessages([
                        'required' => 'Mohon jelaskan data yang dibutuhkan',
                    ])
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
                    ->placeholder('📁 Tarik & letakkan file di sini atau klik untuk memilih (PDF, Excel, JPG, PNG — maks 2MB)')
                    ->hintIcon('heroicon-m-question-mark-circle')
                    ->hintColor('warning')
                    ->hint('Hanya PDF, Excel (.xls/.xlsx), JPG, JPEG, PNG. Maks 2MB.')
                    // ── LAPISAN 1: MIME type yang diizinkan ──────────────────────────────
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/vnd.ms-excel',                                         // .xls
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'image/jpeg',
                        'image/png',
                    ])
                    // ── LAPISAN 2: Validasi Laravel rules (ekstensi + ukuran) ───────────
                    ->rules([
                        'file',
                        'max:2048', // 2 MB
                        // Hanya izinkan ekstensi berikut
                        'mimes:pdf,xls,xlsx,jpg,jpeg,png',
                        // Tolak ekstensi berbahaya secara eksplisit
                        function (string $attribute, mixed $value, \Closure $fail) {
                            // Ekstensi yang DILARANG
                            $blockedExtensions = [
                                'php',
                                'php3',
                                'php4',
                                'php5',
                                'php7',
                                'php8',
                                'phtml',
                                'phar',
                                'sh',
                                'bash',
                                'py',
                                'rb',
                                'pl',
                                'exe',
                                'bat',
                                'cmd',
                                'com',
                                'htaccess',
                                'htpasswd',
                                'ini',
                                'conf',
                                'config',
                                'js',
                                'jsp',
                                'asp',
                                'aspx',
                                'cgi',
                                'shtml',
                                'svg',  // SVG bisa menyisipkan JS
                            ];

                            if (!$value || !($value instanceof \Illuminate\Http\UploadedFile)) {
                                return;
                            }

                            $ext = strtolower($value->getClientOriginalExtension());

                            if (in_array($ext, $blockedExtensions)) {
                                $fail("❌ File dengan ekstensi '.{$ext}' tidak diizinkan demi keamanan sistem.");
                                return;
                            }

                            // ── LAPISAN 3: Cek magic bytes (isi file sungguhan) ───────────
                            // Pastikan isi file sesuai dengan ekstensinya, bukan sekadar rename
                            $realMime = $value->getMimeType();
                            $allowedMimes = [
                                'application/pdf',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'image/jpeg',
                                'image/png',
                            ];

                            if (!in_array($realMime, $allowedMimes)) {
                                $fail("❌ Jenis file '{$realMime}' tidak diizinkan. Hanya PDF, Excel, JPG, JPEG, PNG.");
                                return;
                            }

                            // ── LAPISAN 4: Cek konten PHP tag dalam file ──────────────────
                            // Tangkap file yang me-rename diri tapi berisi kode PHP
                            $content = file_get_contents($value->getRealPath());
                            $phpPatterns = ['<?php', '<?=', '<? ', 'eval(', 'exec(', 'system(', 'passthru(', 'shell_exec('];

                            foreach ($phpPatterns as $pattern) {
                                if (stripos($content, $pattern) !== false) {
                                    $fail('❌ File terdeteksi mengandung skrip berbahaya dan ditolak.');
                                    return;
                                }
                            }
                        },
                    ])
                    ->maxSize(2048) // 2MB
                    ->directory('Surat')
                    ->disk('public')
                    ->visibility('public')
                    ->required()
            ]);
    }

    //Isi tampilan
    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum ada data')
            ->emptyStateDescription('Silakan tambahkan data baru.')
            ->emptyStateIcon('heroicon-o-clipboard-document')
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
                        default => 'primary',
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
            ->poll('1s')

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
