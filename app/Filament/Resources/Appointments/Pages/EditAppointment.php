<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('appointments.update'),
            403
        );

        parent::mount($record);
    }

    public function getTitle(): string
    {
        return 'تعديل الموعد';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض')
                ->icon('heroicon-o-eye')
                ->visible(fn (): bool => auth()->user()?->can('appointments.view') ?? false),

            DeleteAction::make()
                ->label('حذف')
                ->visible(fn (): bool => auth()->user()?->can('appointments.delete') ?? false),

            ForceDeleteAction::make()
                ->label('حذف نهائي')
                ->visible(fn (): bool => auth()->user()?->can('appointments.force_delete') ?? false),

            RestoreAction::make()
                ->label('استعادة')
                ->visible(fn (): bool => auth()->user()?->can('appointments.restore') ?? false),
        ];
    }
}
