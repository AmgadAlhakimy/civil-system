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

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'certificate_number';

    protected static ?string $modelLabel = 'شهادة وفاة';

    protected static ?string $pluralModelLabel = 'شهادات الوفاة';

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    public static function getRecordTitle($record): string
    {
        return 'بيانات شهادة الوفاة: ' . $record->certificate_number;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('death_certificates.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('death_certificates.create') ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->can('death_certificates.view') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('death_certificates.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('death_certificates.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('death_certificates.delete') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('death_certificates.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('death_certificates.restore') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('death_certificates.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('death_certificates.force_delete') ?? false;
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
