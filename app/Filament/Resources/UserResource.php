<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;


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
                fn($query) => $query->where('id', $user->id)
            );
    }
    //------------------end off session check------------------


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(fn(string $context) => $context === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    // ->required()
                    // ->visible(function (string $operation) {
                    //     $user = Filament::auth()->user();

                    //     // admin selalu boleh lihat
                    //     if ($user?->role === 'admin') {
                    //         return true;
                    //     }

                    //     // selain admin hanya saat create
                    //     return $operation === 'create';
                    // })
                    ->required(fn(string $context) => $context === 'create')
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->maxLength(255),


                Forms\Components\Select::make('role')
                    ->label('Role')
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
                // ->options(function () {
                //     $user = Filament::auth()->user();

                //     $options = [
                //         'user' => 'User',
                //         'sekre' => 'Sekre',
                //     ];

                //     // Tampilkan opsi Admin hanya jika user yang login adalah admin
                //     if ($user && $user->email === 'admin@admin.com') {
                //         $options['admin'] = 'Admin';
                //     }

                //     return $options;
                // })

                Forms\Components\Select::make('unit_kerja_id')
                    ->label('Unit Kerja')
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
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(isFromZero: false),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'user',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('unitKerja.nama_opd')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_kerja_id')
                    ->label('Unit Kerja')
                    ->relationship('unitKerja', 'nama_opd'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->icon('heroicon-s-pencil')
                    ->extraAttributes([
                        'style' => 'background-color: #facc15; color: black;'
                    ])
                    ->visible(fn() => in_array(auth()->user()?->role, ['admin', 'user', 'sekre'])),

                Tables\Actions\DeleteAction::make()
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
