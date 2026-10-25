<?php

namespace App\Filament\Resources\Citizens;

use App\Filament\Resources\Citizens\Pages\CreateCitizen;
use App\Filament\Resources\Citizens\Pages\EditCitizen;
use App\Filament\Resources\Citizens\Pages\ListCitizens;
use App\Filament\Resources\Citizens\Pages\ViewCitizen;
use App\Filament\Resources\Citizens\Schemas\CitizenForm;
use App\Filament\Resources\Citizens\Schemas\CitizenInfolist;
use App\Filament\Resources\Citizens\Tables\CitizensTable;
use App\Models\Citizen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CitizenResource extends Resource
{
    protected static ?string $model = Citizen::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'مواطن';

    protected static ?string $pluralModelLabel = 'المواطنون';

    public static function getRecordTitle($record): string
    {
        return 'بيانات المواطن: ' . trim(
                "{$record->first_name} {$record->father_name} {$record->middle_name} {$record->last_name}"
            );
    }

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('citizens.view') ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->can('citizens.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('citizens.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('citizens.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('citizens.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('citizens.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return CitizenForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CitizenInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitizensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'father_name',
            'middle_name',
            'last_name',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCitizens::route('/'),
            'create' => CreateCitizen::route('/create'),
            'view' => ViewCitizen::route('/{record}'),
            'edit' => EditCitizen::route('/{record}/edit'),
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
