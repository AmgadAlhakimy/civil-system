<?php

namespace App\Filament\Resources\BirthCertificates;

use App\Filament\Resources\BirthCertificates\Pages\CreateBirthCertificate;
use App\Filament\Resources\BirthCertificates\Pages\EditBirthCertificate;
use App\Filament\Resources\BirthCertificates\Pages\ListBirthCertificates;
use App\Filament\Resources\BirthCertificates\Pages\ViewBirthCertificate;
use App\Filament\Resources\BirthCertificates\Schemas\BirthCertificateForm;
use App\Filament\Resources\BirthCertificates\Schemas\BirthCertificateInfolist;
use App\Filament\Resources\BirthCertificates\Tables\BirthCertificatesTable;
use App\Models\BirthCertificate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BirthCertificateResource extends Resource
{
    protected static ?string $model = BirthCertificate::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'certificate_number';

    protected static ?string $modelLabel = 'شهادة ميلاد';

    protected static ?string $pluralModelLabel = 'شهادات الميلاد';

    public static function getNavigationGroup(): ?string
    {
        return 'السجل المدني';
    }

    public static function getRecordTitle($record): string
    {
        return 'بيانات شهادة الميلاد: ' . $record->certificate_number;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('birth_certificates.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('birth_certificates.create') ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->can('birth_certificates.view') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('birth_certificates.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('birth_certificates.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('birth_certificates.delete') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('birth_certificates.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('birth_certificates.restore') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('birth_certificates.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('birth_certificates.force_delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return BirthCertificateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BirthCertificateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BirthCertificatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBirthCertificates::route('/'),
            'create' => CreateBirthCertificate::route('/create'),
            'view' => ViewBirthCertificate::route('/{record}'),
            'edit' => EditBirthCertificate::route('/{record}/edit'),
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
