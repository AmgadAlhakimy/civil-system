<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return 'بيانات الموعد: ' . $this->record->appointment_date;
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('confirm')
                ->label('تأكيد الموعد')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('تأكيد الموعد')
                ->modalDescription(
                    'هل أنت متأكد من تأكيد هذا الموعد؟ بعد التأكيد ستصبح حالة الموعد "مؤكد".'
                )
                ->modalSubmitActionLabel('نعم، تأكيد الموعد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {

                    $this->record->update([
                        'status' => 'confirmed',
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => now(),
                    ]);

                    Notification::make()
                        ->title('تم تأكيد الموعد')
                        ->body('تم تأكيد الموعد بنجاح.')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'status',
                        'confirmed_by',
                        'confirmed_at',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل')
                ->icon('heroicon-o-pencil-square'),

        ];
    }
}
