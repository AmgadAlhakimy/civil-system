<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use App\Models\Passport;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPassport extends EditRecord
{
    protected static string $resource = PassportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', [
            'record' => $this->getRecord(),
        ]);
    }
    protected function handleRecordUpdate(
        \Illuminate\Database\Eloquent\Model $record,
        array $data
    ): Passport {
        $existingPassport = Passport::query()
            ->where('citizen_id', $data['citizen_id'])
            ->where('type', $data['type'])
            ->where('id', '!=', $record->id)
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($existingPassport) {
            Notification::make()
                ->danger()
                ->title('لا يمكن تعديل الجواز')
                ->body('هذا المواطن لديه بالفعل جواز من نفس النوع قيد المعالجة أو ساري المفعول.')
                ->persistent()
                ->send();

            $this->halt();
        }

        $record->fill([
            'citizen_id' => $data['citizen_id'],
            'passport_number' => $data['passport_number'],
            'type' => $data['type'],
            'notes' => $data['notes'] ?? null,
        ]);

        $record->save();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض الجواز')
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->label('حذف الجواز')
                ->icon('heroicon-o-trash'),

            ForceDeleteAction::make()
                ->label('حذف نهائي'),

            RestoreAction::make()
                ->label('استعادة الجواز'),
        ];
    }
}
