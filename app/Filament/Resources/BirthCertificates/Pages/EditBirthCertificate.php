<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBirthCertificate extends EditRecord
{
    protected static string $resource = BirthCertificateResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('birth_certificates.update'),
            403
        );

        parent::mount($record);
    }

    public function getTitle(): string
    {
        return 'تعديل شهادة الميلاد';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض')
                ->icon('heroicon-o-eye')
                ->visible(
                    fn (): bool => auth()->user()?->can('birth_certificates.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف')
                ->visible(
                    fn (): bool => auth()->user()?->can('birth_certificates.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->label('حذف نهائي')
                ->visible(
                    fn (): bool => auth()->user()?->can('birth_certificates.force_delete') ?? false
                ),

            RestoreAction::make()
                ->label('استعادة')
                ->visible(
                    fn (): bool => auth()->user()?->can('birth_certificates.restore') ?? false
                ),
        ];
    }
}
