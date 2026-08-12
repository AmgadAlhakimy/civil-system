<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPassport extends ViewRecord
{
    protected static string $resource = PassportResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('approve')
                ->label('اعتماد الجواز')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد الجواز')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد هذا الجواز؟'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {

                    $this->record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);

                    Notification::make()
                        ->title('تم اعتماد الجواز بنجاح')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                    ]);
                }),

            Action::make('reject')
                ->label('رفض الجواز')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('رفض الجواز')
                ->modalDescription(
                    'هل أنت متأكد من رفض هذا الجواز؟'
                )
                ->modalSubmitActionLabel('نعم، رفض')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {

                    $this->record->update([
                        'status' => 'rejected',
                    ]);

                    Notification::make()
                        ->title('تم رفض الجواز')
                        ->danger()
                        ->send();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل الجواز')
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
