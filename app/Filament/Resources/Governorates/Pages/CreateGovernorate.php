<?php

namespace App\Filament\Resources\Governorates\Pages;

use App\Filament\Resources\Governorates\GovernorateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGovernorate extends CreateRecord
{
    protected static string $resource = GovernorateResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('governorates.create'),
            403
        );

        parent::mount();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
