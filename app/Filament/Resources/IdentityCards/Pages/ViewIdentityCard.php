<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIdentityCard extends ViewRecord
{
    protected static string $resource = IdentityCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('اعتماد البطاقة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد البطاقة الشخصية')
                ->modalDescription('هل أنت متأكد من اعتماد هذه البطاقة؟')
                ->modalSubmitActionLabel('نعم، اعتماد البطاقة')
                ->visible(fn () => $this->record->status === 'pending')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'active',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل'),
        ];
    }
}
