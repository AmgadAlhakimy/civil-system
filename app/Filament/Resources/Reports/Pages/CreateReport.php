<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use App\Services\ReportService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $filters = [
            'gender' => $data['gender'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'from_date' => $data['from_date'] ?? null,
            'to_date' => $data['to_date'] ?? null,
            'status' => $data['status_filter'] ?? null,
        ];

        $filters = array_filter(
            $filters,
            fn ($value) => $value !== null && $value !== ''
        );

        $format = match ($data['format'] ?? null) {
            'excel' => 'xlsx',
            default => $data['format'] ?? 'pdf',
        };

        try {
            $report = app(ReportService::class)->generate(
                reportType: $data['report_type'],
                format: $format,
                filters: $filters,
            );

            Notification::make()
                ->title('تم إنشاء التقرير بنجاح')
                ->success()
                ->send();

            return $report;
        } catch (\Throwable $e) {
            Notification::make()
                ->title('فشل إنشاء التقرير')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            throw $e;
        }
    }
}
