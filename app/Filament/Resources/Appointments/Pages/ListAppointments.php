<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('appointments.view'),
            403
        );

        parent::mount();
    }

    public function getTitle(): string
    {
        return 'المواعيد';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('حجز موعد')
                ->icon('heroicon-o-calendar-days')
                ->visible(fn (): bool => auth()->user()?->can('appointments.create') ?? false),
        ];
    }
}
