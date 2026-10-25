<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('reports.view'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label('تحميل التقرير')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(
                    fn (): bool =>
                        (auth()->user()?->can('reports.download') ?? false)
                        && $this->record->status === 'completed'
                        && filled($this->record->path)
                        && Storage::disk('local')->exists($this->record->path)
                )
                ->action(function () {
                    abort_unless(
                        auth()->user()?->can('reports.download'),
                        403
                    );

                    abort_unless(
                        $this->record->status === 'completed'
                        && filled($this->record->path)
                        && Storage::disk('local')->exists($this->record->path),
                        404
                    );

                    return Storage::disk('local')->download(
                        $this->record->path,
                        basename($this->record->path)
                    );
                }),

            EditAction::make()
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('reports.update') ?? false
                ),
        ];
    }
}
