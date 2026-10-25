<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBirthCertificates extends ListRecords
{
    protected static string $resource = BirthCertificateResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('birth_certificates.view'),
            403
        );

        parent::mount();
    }

    public function getTitle(): string
    {
        return 'شهادات الميلاد';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إصدار شهادة ميلاد')
                ->icon('heroicon-o-document-plus')
                ->visible(
                    fn (): bool => auth()->user()?->can('birth_certificates.create') ?? false
                ),
        ];
    }
}
