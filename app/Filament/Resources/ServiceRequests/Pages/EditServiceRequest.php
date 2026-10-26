<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('service_requests.update'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_requests.view') ?? false
                ),

            DeleteAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_requests.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_requests.force_delete') ?? false
                ),

            RestoreAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_requests.restore') ?? false
                ),
        ];
    }
}
