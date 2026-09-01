<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class UserResource extends Resource
{
    protected static ?string $model = User::class;



    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $modelLabel = 'Tambah User';
    protected static ?string $pluralModelLabel = 'User';
    protected static ?string $navigationGroup = 'Manajemen Pengguna & Unit Kerja';
    protected static ?int $navigationSort = 3;

    //login session check, admin full akses, user hanya bisa akses data sendiri


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->when(
                $user->role !== 'admin',
                fn($query) =>
                $query->where('id', $user->id)
                    ->orWhere('created_by', $user->id)
            );
    }

    //------------------end off session check------------------


    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user->role === 'admin' || $user->id === $record->id;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(fn(string $context) => $context === 'create')
                    ->placeholder('Mohon isikan nama pengguna')
                    ->validationMessages([
                        'required' => 'Mohon isikan nama pengguna.',
                    ])
                    ->extraAttributes([
                        'novalidate' => 'novalidate',
                    ])
                    ->maxLength(255),

                Forms\Components\TextInput::make('username')
                    ->label('Username Login')
                    ->helperText('Klik ikon 🔑 untuk generate username')
                    //->placeholder('Klik ikon kunci atau isi manual...')
                    ->required(fn(string $context) => $context === 'create')
                    ->validationMessages([
                        'required' => 'Username wajib diisi.',
                        'unique' => 'Username sudah digunakan, pilih username lain.',
                    ])
                    ->unique(table: 'users', column: 'username', ignoreRecord: true)
                    ->rules(['regex:/^[a-z0-9_]+$/'])
                    ->validationMessages([
                        'regex' => 'Username hanya boleh huruf kecil, angka, dan underscore (_).',
                    ])
                    ->maxLength(50)
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('generateUsername')
                            ->label('Generate Username')
                            ->icon('heroicon-o-key')
                            ->color('warning')
                            ->tooltip('Klik untuk generate username')
                            ->action(function (Forms\Set $set) {
                                // Generate username unik: usr_ + random string 8 karakter
                                do {
                                    $username = 'usr_' . \Illuminate\Support\Str::lower(
                                        \Illuminate\Support\Str::random(8)
                                    );
                                } while (\App\Models\User::where('username', $username)->exists());

                                $set('username', $username);
                            })
                    ),


                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->validationMessages([
                        'required' => 'Mohon isikan email pengguna.',
                        'email' => 'Mohon isikan email yang valid.',
                    ])
                    ->required(fn(string $context) => $context === 'create')
                    ->placeholder('contoh: xxxxxx@gmail.com')
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->validationMessages([
                        'required' => 'Mohon isikan password pengguna.',
                    ])
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->maxLength(255)
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('generatePassword')
                            ->label('Generate')
                            ->icon('heroicon-o-key')
                            ->color('info')
                            ->tooltip('Klik untuk generate password otomatis')
                            ->action(function (Set $set) {
                                $generated = Str::password(12, letters: true, numbers: true, symbols: false, spaces: false);
                                $set('password', $generated);
                                $set('password_preview', $generated);
                            })
                    ),

                Forms\Components\TextInput::make('password_preview')
                    ->label('Password yang Di-generate (Catat / Copy)')
                    ->helperText('Password ini hanya tampil sekali untuk dicatat. Tidak disimpan ke database.')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn(Forms\Get $get) => filled($get('password_preview')))
                    ->extraInputAttributes(['style' => 'font-family: monospace; letter-spacing: 2px; font-size: 1.05rem;']),

                Forms\Components\TextInput::make('no_hp')
                    ->label('No HP')
                    ->validationMessages([
                        'required' => 'Mohon isikan nomor HP pengguna.',
                        'regex' => 'Nomor HP harus diawali 628 dan hanya berisi angka.',
                    ])
                    ->tel()
                    ->prefix('628')
                    ->rules(['regex:/^628[0-9]{8,11}$/'])
                    ->placeholder('628xxxxxxxx')
                    ->required(fn(string $operation) => $operation === 'create') //wajib isikan saat create
                    ->maxLength(15),

                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->validationMessages([
                        'required' => 'Mohon pilih role pengguna.',
                    ])
                    ->options([
                        'user' => 'User',
                        'sekre' => 'Sekre',
                        'admin' => 'Admin',
                    ])
                    ->hidden(fn() => Filament::auth()->user()->role !== 'admin')
                    ->required()
                    // ->visible(fn(string $operation) => $operation === 'create')
                    ->default('user')
                    ->disabled(fn() => !optional(Filament::auth()->user())->email === 'admin@admin.com') // Opsional: nonaktifkan field jika bukan admin
                    ->dehydrated(fn() => optional(Filament::auth()->user())->email === 'admin@admin.com'), // Pastikan nilai tidak dikirim jika bukan admin

                Forms\Components\Select::make('unit_kerja_id')
                    ->label('Unit Kerja')
                    ->validationMessages([
                        'required' => 'Mohon pilih unit kerja pengguna.',
                    ])
                    ->relationship('unitKerja', 'nama_opd')
                    ->searchable()
                    ->preload()
                    ->visible(function (string $operation) {
                        $user = Filament::auth()->user();

                        // admin selalu boleh lihat
                        if ($user?->role === 'admin') {
                            return true;
                        }

                        // selain admin hanya saat create
                        return $operation === 'create';
                    })

                    ->required(fn(Forms\Get $get) => $get('role') === 'user')
                    ->hidden(fn(Forms\Get $get) => $get('role') === 'admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum ada data')
            ->emptyStateDescription('Silakan tambahkan data baru.')
            ->emptyStateIcon('heroicon-o-user')
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(isFromZero: false),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('email')
                //     ->label('Email')
                //     ->searchable(),

                // Tables\Columns\TextColumn::make('no_hp')
                //     ->label('No HP')
                //     ->searchable(),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'user',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('unitKerja.nama_opd')
                    ->label('Unit Kerja')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('created_at')
                //     ->label('Dibuat pada')
                //     ->dateTime()
                //->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('unit_kerja_id')
                    ->label('Unit Kerja')
                    ->relationship('unitKerja', 'nama_opd'),



            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->visible(
                        fn($record) =>
                        auth()->user()->role === 'admin' ||
                        $record->id === auth()->id()
                    )
                    ->icon('heroicon-s-pencil')
                    ->extraAttributes([
                        'style' => 'background-color: #facc15; color: black;'
                    ])
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user', 'sekre'])),

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

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
