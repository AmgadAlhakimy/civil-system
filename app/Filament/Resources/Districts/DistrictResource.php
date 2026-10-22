<?php

namespace App\Filament\Resources\Districts;

use App\Filament\Resources\Districts\Pages\CreateDistrict;
use App\Filament\Resources\Districts\Pages\EditDistrict;
use App\Filament\Resources\Districts\Pages\ListDistricts;
use App\Filament\Resources\Districts\Schemas\DistrictForm;
use App\Filament\Resources\Districts\Tables\DistrictsTable;
use App\Models\District;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    // ═══════════════════════════════════════════════════════════
    // الأيقونة والترتيب
    // ═══════════════════════════════════════════════════════════
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'name_ar';

    protected static ?int $navigationSort = 2;

    // ═══════════════════════════════════════════════════════════
    // النماذج والجداول
    // ═══════════════════════════════════════════════════════════
    public static function form(Schema $schema): Schema
    {
        return DistrictForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DistrictsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDistricts::route('/'),
            'create' => CreateDistrict::route('/create'),
            'edit' => EditDistrict::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // ═══════════════════════════════════════════════════════════
    // التسميات العربية
    // ═══════════════════════════════════════════════════════════
    public static function getModelLabel(): string
    {
        return 'مديرية';
    }

    public static function getPluralModelLabel(): string
    {
        return 'المديريات';
    }

    public static function getNavigationLabel(): string
    {
        return 'المديريات';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'المواقع الجغرافية';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}