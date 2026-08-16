<?php

namespace App\Filament\Resources\DeathCertificates\Pages;

use App\Filament\Resources\DeathCertificates\DeathCertificateResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDeathCertificate extends ViewRecord
{
    protected static string $resource = DeathCertificateResource::class;

    public function getTitle(): string
    {
        return 'بيانات شهادة الوفاة: ' . $this->record->certificate_number;
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('approve')
                ->label('اعتماد الشهادة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد شهادة الوفاة')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد شهادة الوفاة؟ بعد الاعتماد ستصبح الشهادة معتمدة.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn ($record): bool => $record->status === 'pending')
                ->action(function ($record): void {
                    $record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                }),

            EditAction::make()
                ->label('تعديل')
                ->icon('heroicon-o-pencil-square'),

        ];
    }
}
