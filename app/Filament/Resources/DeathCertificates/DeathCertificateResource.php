<?php

namespace App\Filament\Resources\DeathCertificates;

use App\Filament\Resources\DeathCertificates\Pages\CreateDeathCertificate;
use App\Filament\Resources\DeathCertificates\Pages\EditDeathCertificate;
use App\Filament\Resources\DeathCertificates\Pages\ListDeathCertificates;
use App\Filament\Resources\DeathCertificates\Pages\ViewDeathCertificate;
use App\Filament\Resources\DeathCertificates\Schemas\DeathCertificateForm;
use App\Filament\Resources\DeathCertificates\Schemas\DeathCertificateInfolist;
use App\Filament\Resources\DeathCertificates\Tables\DeathCertificatesTable;
use App\Models\DeathCertificate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeathCertificateResource extends Resource
{
    protected static ?string $model = DeathCertificate::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    protected static ?string $recordTitleAttribute = 'certificate_number';

    protected static ?string $modelLabel = 'شهادة وفاة';

    protected static ?string $pluralModelLabel = 'شهادات الوفاة';

    public static function getRecordTitle($record): string
    {
        return 'بيانات شهادة الوفاة: ' . $record->certificate_number;
    }

    public static function form(Schema $schema): Schema
    {
        return DeathCertificateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DeathCertificateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeathCertificatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeathCertificates::route('/'),
            'create' => CreateDeathCertificate::route('/create'),
            'view' => ViewDeathCertificate::route('/{record}'),
            'edit' => EditDeathCertificate::route('/{record}/edit'),
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
