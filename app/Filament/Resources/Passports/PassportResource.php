<?php

namespace App\Filament\Resources\Passports;

use App\Filament\Resources\Passports\Pages\CreatePassport;
use App\Filament\Resources\Passports\Pages\EditPassport;
use App\Filament\Resources\Passports\Pages\ListPassports;
use App\Filament\Resources\Passports\Pages\ViewPassport;
use App\Filament\Resources\Passports\Schemas\PassportForm;
use App\Filament\Resources\Passports\Schemas\PassportInfolist;
use App\Filament\Resources\Passports\Tables\PassportsTable;
use App\Models\Passport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PassportResource extends Resource
{
    protected static ?string $model = Passport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    protected static ?string $recordTitleAttribute = 'passport_number';

    protected static ?string $modelLabel = 'جواز';

    protected static ?string $pluralModelLabel = 'الجوازات';

    public static function getRecordTitle($record): string
    {
        return 'بيانات الجواز: ' . $record->passport_number;
    }

    public static function form(Schema $schema): Schema
    {
        return PassportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PassportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PassportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPassports::route('/'),
            'create' => CreatePassport::route('/create'),
            'view' => ViewPassport::route('/{record}'),
            'edit' => EditPassport::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
