<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('reports.update'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('reports.view') ?? false
                ),

            DeleteAction::make()
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('reports.delete') ?? false
                ),
        ];
    }
}
