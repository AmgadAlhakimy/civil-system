<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use App\Models\Citizen;
use App\Models\IdentityCard;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateIdentityCard extends CreateRecord
{
    protected static string $resource = IdentityCardResource::class;

    public function getTitle(): string
    {
        return 'إصدار بطاقة شخصية';
    }

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('identity_cards.create'),
            403
        );

        parent::mount();
    }

    protected function handleRecordCreation(array $data): IdentityCard
    {
        return DB::transaction(function () use ($data): IdentityCard {
            $citizen = Citizen::query()
                ->whereKey($data['citizen_id'])
                ->where('verification_status', 'approved')
                ->first();

            if (! $citizen) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار البطاقة')
                    ->body('لا يمكن إصدار بطاقة شخصية إلا لمواطن تم اعتماد بياناته.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            $existingCard = IdentityCard::query()
                ->where('citizen_id', $data['citizen_id'])
                ->whereIn('status', ['pending', 'active'])
                ->lockForUpdate()
                ->exists();

            if ($existingCard) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار البطاقة')
                    ->body('هذا المواطن لديه بالفعل بطاقة شخصية قيد المعالجة أو سارية المفعول.')
                    ->send();

                $this->halt();
            }

            $card = new IdentityCard();

            $card->fill($data);

            $issueDate = now();

            $card->status = 'active';
            $card->issued_by = auth()->id();
            $card->print_count = 0;
            $card->issue_date = $issueDate;
            $card->expiry_date = $issueDate->copy()->addYears(
                $card->validityYears()
            );
            $card->approved_by = auth()->id();
            $card->approved_at = $issueDate;

            $card->save();

            return $card;
        });
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إصدار البطاقة الشخصية بنجاح';
    }
}
