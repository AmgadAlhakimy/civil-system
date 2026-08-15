<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewFamilyCard extends ViewRecord
{
    protected static string $resource = FamilyCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('اعتماد البطاقة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد البطاقة العائلية')
                ->modalDescription('هل أنت متأكد من اعتماد هذه البطاقة؟ بعد الاعتماد ستصبح البطاقة سارية.')
                ->modalSubmitActionLabel('اعتماد')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'active',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);

                    Notification::make()
                        ->title('تم اعتماد البطاقة')
                        ->body('تم اعتماد البطاقة العائلية وأصبحت سارية.')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                    ]);
                }),

            EditAction::make(),
        ];
    }
}
