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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditPassport extends EditRecord
{
    protected static string $resource = PassportResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (! auth()->user()?->can("passports.edit")) {
            throw new AuthorizationException(
                "ليس لديك صلاحية تعديل بيانات الجواز."
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl("view", [
            "record" => $this->getRecord(),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $citizen = $this->getRecord()->citizen;

        if (! $citizen) {
            return $data;
        }

        $data["citizen_first_name_en"] = $citizen->first_name_en;
        $data["citizen_father_name_en"] = $citizen->father_name_en;
        $data["citizen_middle_name_en"] = $citizen->middle_name_en;
        $data["citizen_last_name_en"] = $citizen->last_name_en;
        $data["citizen_birth_place_en"] = $citizen->birth_place_en;
        $data["citizen_occupation_en"] = $citizen->occupation_en;

        return $data;
    }

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Passport {
        if (! $record instanceof Passport) {
            throw new \RuntimeException(
                "السجل المحدد ليس جواز سفر صالحًا."
            );
        }

        return DB::transaction(function () use ($record, $data): Passport {
            $citizen = $record->citizen()
                ->lockForUpdate()
                ->firstOrFail();

            $citizen->first_name_en = $data["citizen_first_name_en"];
            $citizen->father_name_en = $data["citizen_father_name_en"];
            $citizen->middle_name_en = $data["citizen_middle_name_en"];
            $citizen->last_name_en = $data["citizen_last_name_en"];
            $citizen->birth_place_en = $data["citizen_birth_place_en"];
            $citizen->occupation_en = $data["citizen_occupation_en"];

            $citizen->save();

            $record->issuing_authority_ar = $data["issuing_authority_ar"];
            $record->issuing_authority_en = $data["issuing_authority_en"];
            $record->notes = $data["notes"] ?? null;

            $record->save();

            return $record;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label("عرض الجواز")
                ->icon("heroicon-o-eye")
                ->visible(
                    fn (): bool => auth()->user()?->can("passports.view") ?? false
                ),

            DeleteAction::make()
                ->label("حذف الجواز")
                ->icon("heroicon-o-trash")
                ->visible(
                    fn (): bool => auth()->user()?->can("passports.delete") ?? false
                ),

            ForceDeleteAction::make()
                ->label("حذف نهائي")
                ->visible(
                    fn (): bool => auth()->user()?->can("passports.force_delete") ?? false
                ),

            RestoreAction::make()
                ->label("استعادة الجواز")
                ->visible(
                    fn (): bool => auth()->user()?->can("passports.restore") ?? false
                ),
        ];
    }
}
