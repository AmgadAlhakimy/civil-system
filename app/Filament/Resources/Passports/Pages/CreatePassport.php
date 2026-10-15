<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use App\Models\Citizen;
use App\Models\Passport;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

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
        }

        return DB::transaction(function () use ($data): Passport {
            $citizen = Citizen::query()
                ->findOrFail($data['citizen_id']);

            $citizen->first_name_en = $data['citizen_first_name_en'];
            $citizen->father_name_en = $data['citizen_father_name_en'];
            $citizen->middle_name_en = $data['citizen_middle_name_en'];
            $citizen->last_name_en = $data['citizen_last_name_en'];
            $citizen->birth_place_en = $data['citizen_birth_place_en'];
            $citizen->occupation_en = $data['citizen_occupation_en'];

            $citizen->save();

            $issueDate = now();

            $passport = new Passport();

            $passport->citizen_id = $data['citizen_id'];
            $passport->passport_number = $data['passport_number'];
            $passport->type = $data['type'];
            $passport->issuing_authority_ar = $data['issuing_authority_ar'];
            $passport->issuing_authority_en = $data['issuing_authority_en'];
            $passport->notes = $data['notes'] ?? null;

            $passport->issued_by = auth()->id();

            $passport->status = 'active';
            $passport->issue_date = $issueDate->toDateString();
            $passport->expiry_date = $issueDate
                ->copy()
                ->addYears($passport->validityYears())
                ->toDateString();

            $passport->approved_by = auth()->id();
            $passport->approved_at = $issueDate;
            $passport->print_count = 0;

            $passport->save();

            return $passport;
        });
    }
}
