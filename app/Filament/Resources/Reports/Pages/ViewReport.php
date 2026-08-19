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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label('تحميل التقرير')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => filled($this->record->path))
                ->action(function () {
                    return Storage::disk('local')->download(
                        $this->record->path
                    );
                }),

            EditAction::make(),
        ];
    }
}
