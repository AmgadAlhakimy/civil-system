<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Resources\Appointments\Pages\ViewAppointment;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Resources\Appointments\Schemas\AppointmentInfolist;
use App\Filament\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'appointment_date';

    protected static ?string $modelLabel = 'موعد';

    protected static ?string $pluralModelLabel = 'المواعيد';

    public static function getNavigationGroup(): ?string
    {
        return 'الخدمات والمعاملات';
    }

    public static function getRecordTitle($record): string
    {
        $citizenName = trim(
            "{$record->citizen?->first_name} {$record->citizen?->father_name} {$record->citizen?->middle_name} {$record->citizen?->last_name}"
        );

        return 'بيانات الموعد: ' . ($citizenName ?: 'غير محدد');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('appointments.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('appointments.create') ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->can('appointments.view') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('appointments.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('appointments.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('appointments.delete') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('appointments.restore') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('appointments.restore') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('appointments.force_delete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('appointments.force_delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AppointmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'view' => ViewAppointment::route('/{record}'),
            'edit' => EditAppointment::route('/{record}/edit'),
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
