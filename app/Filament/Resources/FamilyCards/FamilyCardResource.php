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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FamilyCardResource extends Resource
{
    protected static ?string $model = FamilyCard::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 4;

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
