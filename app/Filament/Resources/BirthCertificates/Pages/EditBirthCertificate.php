<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Actions\DeleteAction;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset(
            $data['issued_by'],
            $data['issue_date'],
            $data['approved_by'],
            $data['approved_at'],
            $data['print_count'],
            $data['status']
        );

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return BirthCertificateResource::getUrl('view', [
            'record' => $this->getRecord(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض الشهادة')
                ->icon('heroicon-o-eye')
                ->visible(
                    fn (): bool => auth()->user()?->can('birth_certificates.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف الشهادة')
                ->icon('heroicon-o-trash')
                ->visible(
                    fn (): bool => auth()->user()?->can('birth_certificates.delete') ?? false
                ),
        ];
    }
}
