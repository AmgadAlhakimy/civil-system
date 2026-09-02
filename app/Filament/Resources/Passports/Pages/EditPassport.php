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

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): Passport
    {
        $existingPassport = Passport::query()
            ->where('citizen_id', $data['citizen_id'])
            ->where('type', $data['type'])
            ->where('id', '!=', $record->id)
            ->whereIn('status', ['pending', 'approved', 'active'])
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

        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض الجواز'),

            DeleteAction::make()
                ->label('حذف الجواز'),

            ForceDeleteAction::make()
                ->label('حذف نهائي'),

            RestoreAction::make()
                ->label('استعادة الجواز'),
        ];
    }
}
