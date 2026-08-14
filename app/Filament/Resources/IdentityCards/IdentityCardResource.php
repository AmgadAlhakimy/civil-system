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

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    protected static ?string $recordTitleAttribute = 'id_number';

    protected static ?string $modelLabel = 'بطاقة شخصية';

    protected static ?string $pluralModelLabel = 'البطاقات الشخصية';

    public static function getRecordTitle($record): string
    {
        return 'بيانات البطاقة الشخصية: ' . $record->id_number;
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
