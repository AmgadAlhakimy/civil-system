<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use App\Models\Passport;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePassport extends CreateRecord
{
    protected static string $resource = PassportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Passport
    {
        $existingPassport = Passport::query()
            ->where('citizen_id', $data['citizen_id'])
            ->where('type', $data['type'])
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($existingPassport) {
            Notification::make()
                ->danger()
                ->title('لا يمكن إنشاء الجواز')
                ->body('هذا المواطن لديه بالفعل طلب جواز من نفس النوع قيد المعالجة أو جواز ساري المفعول.')
                ->send();

            $this->halt();

            return new Passport();
        }

        $passport = new Passport();

        $passport->fill([
            'citizen_id' => $data['citizen_id'],
            'passport_number' => $data['passport_number'],
            'type' => $data['type'],
            'notes' => $data['notes'] ?? null,
        ]);

        $passport->issued_by = auth()->id();
        $passport->status = 'pending';
        $passport->print_count = 0;
        $passport->issue_date = null;
        $passport->expiry_date = null;
        $passport->approved_by = null;
        $passport->approved_at = null;

        $passport->save();

        return $passport;
    }
}
