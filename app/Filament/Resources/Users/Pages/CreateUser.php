<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('users.create') ?? false;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['must_change_password'] = true;

        if (($data['account_type'] ?? null) === 'external') {
            $data['branch_id'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
