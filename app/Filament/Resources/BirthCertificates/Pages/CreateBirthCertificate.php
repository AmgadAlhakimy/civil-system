<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use App\Models\BirthCertificate;
use App\Models\Citizen;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateBirthCertificate extends CreateRecord
{
    protected static string $resource = BirthCertificateResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('birth_certificates.create'),
            403
        );

        parent::mount();
    }

    public function getTitle(): string
    {
        return 'إصدار شهادة ميلاد';
    }

    protected function handleRecordCreation(array $data): BirthCertificate
    {
        return DB::transaction(function () use ($data): BirthCertificate {
            $child = Citizen::query()
                ->whereKey($data['child_id'])
                ->where('verification_status', 'approved')
                ->first();

            $father = Citizen::query()
                ->whereKey($data['father_id'])
                ->where('verification_status', 'approved')
                ->first();

            $mother = Citizen::query()
                ->whereKey($data['mother_id'])
                ->where('verification_status', 'approved')
                ->first();

            if (! $child || ! $father || ! $mother) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار شهادة الميلاد')
                    ->body('يجب أن تكون بيانات الطفل والأب والأم معتمدة قبل إصدار الشهادة.')
                    ->send();

                $this->halt();
            }

            $certificateExists = BirthCertificate::query()
                ->where('child_id', $data['child_id'])
                ->lockForUpdate()
                ->exists();

            if ($certificateExists) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار شهادة الميلاد')
                    ->body('توجد شهادة ميلاد مسجلة لهذا الطفل بالفعل.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            $certificate = new BirthCertificate();

            $certificate->fill($data);

            $issueDate = now();

            $certificate->status = 'issued';
            $certificate->issued_by = auth()->id();
            $certificate->print_count = 0;
            $certificate->issue_date = $issueDate;
            $certificate->approved_by = auth()->id();
            $certificate->approved_at = $issueDate;

            $certificate->save();

            return $certificate;
        });
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إصدار شهادة الميلاد بنجاح';
    }

    protected function getRedirectUrl(): string
    {
        return BirthCertificateResource::getUrl('index');
    }
}
