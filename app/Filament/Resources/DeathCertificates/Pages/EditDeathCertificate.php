<?php

namespace App\Filament\Resources\DeathCertificates\Pages;

use App\Filament\Resources\DeathCertificates\DeathCertificateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDeathCertificate extends EditRecord
{
    protected static string $resource = DeathCertificateResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('death_certificates.update'),
            403
        );

        parent::mount($record);
    }

    public function getTitle(): string
    {
        return 'تعديل شهادة الوفاة';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض')
                ->icon('heroicon-o-eye')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('death_certificates.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('death_certificates.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->label('حذف نهائي')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('death_certificates.force_delete') ?? false
                ),

            RestoreAction::make()
                ->label('استعادة')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('death_certificates.restore') ?? false
                ),
        ];
    }
}
