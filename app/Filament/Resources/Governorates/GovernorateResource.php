<?php

namespace App\Filament\Resources\Governorates;

use App\Filament\Resources\Governorates\Pages\CreateGovernorate;
use App\Filament\Resources\Governorates\Pages\EditGovernorate;
use App\Filament\Resources\Governorates\Pages\ListGovernorates;
use App\Filament\Resources\Governorates\Pages\ViewGovernorate;
use App\Filament\Resources\Governorates\Schemas\GovernorateForm;
use App\Filament\Resources\Governorates\Tables\GovernoratesTable;
use App\Models\Governorate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingLibrary;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('governorates.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('governorates.create') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->can('governorates.view') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('governorates.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('governorates.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('governorates.delete') ?? false;
    }

    public static function canRestore(Model $record): bool
    {
        return auth()->user()?->can('governorates.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('governorates.restore') ?? false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return auth()->user()?->can('governorates.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('governorates.force_delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return GovernorateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GovernoratesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGovernorates::route('/'),
            'create' => CreateGovernorate::route('/create'),
            'view' => ViewGovernorate::route('/{record}'),
            'edit' => EditGovernorate::route('/{record}/edit'),
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
        return 'محافظة';
    }

    public static function getPluralModelLabel(): string
    {
        return 'المحافظات';
    }

    public static function getNavigationLabel(): string
    {
        return 'المحافظات';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الإدارة الجغرافية';
    }
}
