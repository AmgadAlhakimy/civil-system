<?php

namespace App\Filament\Resources\Districts;

use App\Filament\Resources\Districts\Pages\CreateDistrict;
use App\Filament\Resources\Districts\Pages\EditDistrict;
use App\Filament\Resources\Districts\Pages\ListDistricts;
use App\Filament\Resources\Districts\Pages\ViewDistrict;
use App\Filament\Resources\Districts\Schemas\DistrictForm;
use App\Filament\Resources\Districts\Tables\DistrictsTable;
use App\Models\District;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'name_ar';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('districts.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('districts.create') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->can('districts.view') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('districts.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('districts.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('districts.delete') ?? false;
    }

    public static function canRestore(Model $record): bool
    {
        return auth()->user()?->can('districts.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('districts.restore') ?? false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return auth()->user()?->can('districts.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('districts.force_delete') ?? false;
    }

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
            'view' => ViewDistrict::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

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
        return 'الإدارة الجغرافية';
    }
}
