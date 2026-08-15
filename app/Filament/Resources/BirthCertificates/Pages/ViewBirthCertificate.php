<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBirthCertificate extends ViewRecord
{
    protected static string $resource = BirthCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('approve')
                ->label('اعتماد الشهادة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد شهادة الميلاد')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد شهادة الميلاد؟ بعد الاعتماد ستصبح الشهادة سارية.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn ($record): bool => $record->status === 'pending')
                ->action(function ($record): void {
                    $record->update([
                        'status' => 'active',
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
