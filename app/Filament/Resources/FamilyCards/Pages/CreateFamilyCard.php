<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use App\Models\Citizen;
use App\Models\FamilyCard;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateFamilyCard extends CreateRecord
{
    protected static string $resource = FamilyCardResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('family_cards.create'),
            403
        );

        parent::mount();
    }

    public function getTitle(): string
    {
        return 'إصدار بطاقة عائلية';
    }

    protected function handleRecordCreation(array $data): FamilyCard
    {
        return DB::transaction(function () use ($data): FamilyCard {
            $citizen = Citizen::query()
                ->whereKey($data['head_id'])
                ->where('verification_status', 'approved')
                ->first();

            if (! $citizen) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار البطاقة')
                    ->body('لا يمكن إصدار بطاقة عائلية إلا لمواطن تم اعتماد بياناته.')
                    ->send();

                $this->halt();
            }

            $existingCard = FamilyCard::query()
                ->where('head_id', $data['head_id'])
                ->whereIn('status', ['pending', 'approved', 'active'])
                ->lockForUpdate()
                ->exists();

            if ($existingCard) {
                Notification::make()
                    ->danger()
                    ->title('لا يمكن إصدار البطاقة')
                    ->body('رب الأسرة لديه بالفعل بطاقة عائلية قيد المعالجة أو سارية المفعول.')
                    ->send();

                $this->halt();
            }

            $card = new FamilyCard();

            $card->fill($data);

            $issueDate = now();

            $card->status = 'active';
            $card->issued_by = auth()->id();
            $card->print_count = 0;
            $card->issue_date = $issueDate;
            $card->expiry_date = $issueDate->copy()->addYears($card->validityYears());
            $card->approved_by = auth()->id();
            $card->approved_at = $issueDate;

            $card->save();

            return $card;
        });
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إصدار البطاقة العائلية بنجاح';
    }

    protected function getRedirectUrl(): string
    {
        return FamilyCardResource::getUrl('index');
    }
}
