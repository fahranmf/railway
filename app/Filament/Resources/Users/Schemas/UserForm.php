<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Schemas;
use Filament\Forms;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function schema(): array
    {
        return [
            Schemas\Components\Section::make('Informasi Akun')
                ->description('Kelola kredensial pengguna aplikasi.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Alamat Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('role')
                        ->label('Hak Akses (Role)')
                        ->options(UserRole::class) // Mengambil opsi dari Enum
                        ->required()
                        ->default(UserRole::Staff)
                        ->native(false),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // Enkripsi otomatis
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->label('Password Login'),
                ])->columns(2),
        ];
    }
}
