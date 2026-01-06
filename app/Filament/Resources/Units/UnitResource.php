<?php

namespace App\Filament\Resources\Units;

use App\Filament\Resources\Units\Pages;
use App\Filament\Resources\Units\Schemas\UnitForm;
use App\Filament\Resources\Units\Tables\UnitTable;
use App\Models\Unit;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use UnitEnum;
use BackedEnum;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $slug = 'units';
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-beaker';
    protected static UnitEnum|string|null $navigationGroup = 'Data Master';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema(UnitForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(UnitTable::columns())
            ->actions([ EditAction::make(), DeleteAction::make() ])
            ->bulkActions([ BulkActionGroup::make([ DeleteBulkAction::make() ]) ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
