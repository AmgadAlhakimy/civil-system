<?php

namespace App\Filament\Resources\FamilyCards;

use App\Filament\Resources\FamilyCards\Pages\CreateFamilyCard;
use App\Filament\Resources\FamilyCards\Pages\EditFamilyCard;
use App\Filament\Resources\FamilyCards\Pages\ListFamilyCards;
use App\Filament\Resources\FamilyCards\Pages\ViewFamilyCard;
use App\Filament\Resources\FamilyCards\Schemas\FamilyCardForm;
use App\Filament\Resources\FamilyCards\Schemas\FamilyCardInfolist;
use App\Filament\Resources\FamilyCards\Tables\FamilyCardsTable;
use App\Models\FamilyCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FamilyCardResource extends Resource
{
    protected static ?string $model = FamilyCard::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-user-group';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    protected static ?string $recordTitleAttribute = 'card_number';

    protected static ?string $modelLabel = 'بطاقة عائلية';

    protected static ?string $pluralModelLabel = 'البطاقات العائلية';

    public static function getRecordTitle($record): string
    {
        return 'بيانات البطاقة العائلية: ' . $record->card_number;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('family_cards.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('family_cards.create') ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->can('family_cards.view') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('family_cards.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('family_cards.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('family_cards.delete') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('family_cards.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('family_cards.restore') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('family_cards.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('family_cards.force_delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return FamilyCardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FamilyCardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FamilyCardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFamilyCards::route('/'),
            'create' => CreateFamilyCard::route('/create'),
            'view' => ViewFamilyCard::route('/{record}'),
            'edit' => EditFamilyCard::route('/{record}/edit'),
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
