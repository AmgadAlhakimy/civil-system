<?php

namespace App\Filament\Resources\DeathCertificates\Pages;

use App\Filament\Resources\DeathCertificates\DeathCertificateResource;
use App\Models\Citizen;
use App\Models\DeathCertificate;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateDeathCertificate extends CreateRecord
{
    protected static string $resource = DeathCertificateResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('death_certificates.create'),
            403
        );

        parent::mount();
    }

    public function getTitle(): string
    {
        return 'إصدار شهادة وفاة';
    }

    protected function handleRecordCreation(array $data): DeathCertificate
    {
        return DB::transaction(function () use ($data): DeathCertificate {
            $deceased = Citizen::query()
                ->whereKey($data['deceased_id'])
                ->where('verification_status', 'approved')
                ->first();

            if (! $deceased) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار شهادة الوفاة')
                    ->body('يجب أن تكون بيانات المتوفى معتمدة قبل إصدار الشهادة.')
                    ->send();

                $this->halt();
            }

            $certificateExists = DeathCertificate::query()
                ->where('deceased_id', $data['deceased_id'])
                ->lockForUpdate()
                ->exists();

            if ($certificateExists) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار شهادة الوفاة')
                    ->body('توجد شهادة وفاة مسجلة لهذا المواطن بالفعل.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            $certificate = new DeathCertificate();

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
        return 'تم إصدار شهادة الوفاة بنجاح';
    }

    protected function getRedirectUrl(): string
    {
        return DeathCertificateResource::getUrl('index');
    }
}
