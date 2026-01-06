<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas;
use Filament\Forms;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function schema(): array
    {
        return [
            Schemas\Components\Section::make('Data Kategori')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        // Auto-generate Slug saat mengetik nama
                        ->afterStateUpdated(fn ($operation, $state, $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated(),
                ])->columns(2),
        ];
    }
}
