<?php

namespace App\Filament\Resources\IdentityCards;

use App\Filament\Resources\IdentityCards\Pages\CreateIdentityCard;
use App\Filament\Resources\IdentityCards\Pages\EditIdentityCard;
use App\Filament\Resources\IdentityCards\Pages\ListIdentityCards;
use App\Filament\Resources\IdentityCards\Pages\ViewIdentityCard;
use App\Filament\Resources\IdentityCards\Schemas\IdentityCardForm;
use App\Filament\Resources\IdentityCards\Schemas\IdentityCardInfolist;
use App\Filament\Resources\IdentityCards\Tables\IdentityCardsTable;
use App\Models\IdentityCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IdentityCardResource extends Resource
{
    protected static ?string $model = IdentityCard::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedIdentification;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'national_id';

    protected static ?string $modelLabel = 'بطاقة شخصية';

    protected static ?string $pluralModelLabel = 'البطاقات الشخصية';

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    public static function getRecordTitle($record): string
    {
        return 'بيانات البطاقة الشخصية: ' . $record->national_id;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['national_id'];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('identity_cards.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('identity_cards.create') ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->can('identity_cards.view') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('identity_cards.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('identity_cards.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('identity_cards.delete') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('identity_cards.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('identity_cards.restore') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('identity_cards.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('identity_cards.force_delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return IdentityCardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IdentityCardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IdentityCardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIdentityCards::route('/'),
            'create' => CreateIdentityCard::route('/create'),
            'view' => ViewIdentityCard::route('/{record}'),
            'edit' => EditIdentityCard::route('/{record}/edit'),
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
