<?php

namespace App\Filament\Resources\ServiceFees;

use App\Filament\Resources\ServiceFees\Pages\CreateServiceFee;
use App\Filament\Resources\ServiceFees\Pages\EditServiceFee;
use App\Filament\Resources\ServiceFees\Pages\ListServiceFees;
use App\Filament\Resources\ServiceFees\Schemas\ServiceFeeForm;
use App\Filament\Resources\ServiceFees\Tables\ServiceFeesTable;
use App\Models\ServiceFee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceFeeResource extends Resource
{
    protected static ?string $model = ServiceFee::class;

    // الأيقونة والملاحة
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'service_name';

    // الترتيب
    protected static ?int $navigationSort = 10;

    // الصلاحيات الأساسية
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('service_fees.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('service_fees.create') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->can('service_fees.view') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('service_fees.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('service_fees.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('service_fees.delete') ?? false;
    }

    public static function canRestore(Model $record): bool
    {
        return auth()->user()?->can('service_fees.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('service_fees.restore') ?? false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return auth()->user()?->can('service_fees.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('service_fees.force_delete') ?? false;
    }

    // النماذج والجداول
    public static function form(Schema $schema): Schema
    {
        return ServiceFeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceFeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceFees::route('/'),
            'create' => CreateServiceFee::route('/create'),
            'edit' => EditServiceFee::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // تسمية المجموعة بالعربية
    public static function getNavigationGroup(): ?string
    {
        return 'الإعدادات';
    }

    public static function getNavigationLabel(): string
    {
        return 'الرسوم';
    }

    public static function getModelLabel(): string
    {
        return 'خدمة';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الرسوم';
    }
}
