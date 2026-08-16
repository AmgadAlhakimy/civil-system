<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewIdentityCard extends ViewRecord
{
    protected static string $resource = IdentityCardResource::class;

    public function getTitle(): string
    {
        return 'بيانات البطاقة الشخصية: ' . $this->record->card_number;
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('approve')
                ->label('اعتماد البطاقة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد البطاقة الشخصية')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد هذه البطاقة؟ بعد الاعتماد ستصبح البطاقة سارية.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد البطاقة')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'active',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);

                    Notification::make()
                        ->title('تم اعتماد البطاقة الشخصية')
                        ->body('تم اعتماد البطاقة الشخصية وأصبحت سارية.')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل')
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
