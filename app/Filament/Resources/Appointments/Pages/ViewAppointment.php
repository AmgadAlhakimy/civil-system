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

            Action::make('attended')
                ->label('تم الحضور')
                ->icon('heroicon-o-user')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('تسجيل الحضور')
                ->modalDescription(
                    'هل أنت متأكد من تسجيل حضور المواطن لهذا الموعد؟'
                )
                ->modalSubmitActionLabel('نعم، تم الحضور')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'confirmed')
                ->action(function (): void {

                    $this->record->update([
                        'status' => 'attended',
                    ]);

                    Notification::make()
                        ->title('تم تسجيل الحضور')
                        ->body('تم تسجيل حضور المواطن بنجاح.')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            Action::make('no_show')
                ->label('لم يحضر')
                ->icon('heroicon-o-user-minus')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('تسجيل عدم الحضور')
                ->modalDescription(
                    'هل أنت متأكد من أن المواطن لم يحضر لهذا الموعد؟'
                )
                ->modalSubmitActionLabel('نعم، لم يحضر')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'confirmed')
                ->action(function (): void {

                    $this->record->update([
                        'status' => 'no_show',
                    ]);

                    Notification::make()
                        ->title('تم تسجيل عدم الحضور')
                        ->body('تم تسجيل الموعد على أنه لم يحضر.')
                        ->warning()
                        ->send();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            Action::make('cancel')
                ->label('إلغاء الموعد')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('إلغاء الموعد')
                ->modalDescription(
                    'هل أنت متأكد من إلغاء هذا الموعد؟ لا يمكن التراجع عن هذه العملية من هذه الصفحة.'
                )
                ->modalSubmitActionLabel('نعم، إلغاء الموعد')
                ->modalCancelActionLabel('تراجع')
                ->visible(fn (): bool => in_array(
                    $this->record->status,
                    ['pending', 'confirmed'],
                    true
                ))
                ->action(function (): void {

                    $this->record->update([
                        'status' => 'cancelled',
                    ]);

                    Notification::make()
                        ->title('تم إلغاء الموعد')
                        ->body('تم إلغاء الموعد بنجاح.')
                        ->danger()
                        ->send();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn (): bool => in_array(
                    $this->record->status,
                    ['pending', 'confirmed'],
                    true
                )),
        ];
    }
}
