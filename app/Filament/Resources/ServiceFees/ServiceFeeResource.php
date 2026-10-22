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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceFeeResource extends Resource
{
    protected static ?string $model = ServiceFee::class;

    // ═══════════════════════════════════════════════════════════
    // الأيقونة والملاحة
    // ═══════════════════════════════════════════════════════════
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'service_name';

    // ═══════════════════════════════════════════════════════════
    // التسميات العربية
    // ═══════════════════════════════════════════════════════════
    protected static ?string $modelLabel = 'خدمة';

    protected static ?string $pluralModelLabel = 'الرسوم';

    protected static ?string $navigationLabel = 'الرسوم';

    // ═══════════════════════════════════════════════════════════
    // الترتيب
    // ═══════════════════════════════════════════════════════════
    protected static ?int $navigationSort = 10;

    // ═══════════════════════════════════════════════════════════
    // النماذج والجداول
    // ═══════════════════════════════════════════════════════════
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
        return [
            //
        ];
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

    // ═══════════════════════════════════════════════════════════
    // تسمية المجموعة بالعربية (Filament 4)
    // ═══════════════════════════════════════════════════════════
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