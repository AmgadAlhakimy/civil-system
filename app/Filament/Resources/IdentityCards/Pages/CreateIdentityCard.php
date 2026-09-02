<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use App\Models\IdentityCard;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateIdentityCard extends CreateRecord
{
    protected static string $resource = IdentityCardResource::class;

    public function getTitle(): string
    {
        return 'إصدار بطاقة شخصية';
    }

    protected function handleRecordCreation(array $data): IdentityCard
    {
        $existingCard = IdentityCard::query()
            ->where('citizen_id', $data['citizen_id'])
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($existingCard) {
            Notification::make()
                ->danger()
                ->title('لا يمكن إنشاء البطاقة')
                ->body('هذا المواطن لديه بالفعل بطاقة شخصية قيد المعالجة أو سارية المفعول.')
                ->persistent()
                ->send();

            $this->halt();
        }

        $card = new IdentityCard();

        $card->fill($data);
        $card->status = 'pending';
        $card->issued_by = auth()->id();
        $card->print_count = 0;
        $card->issue_date = null;
        $card->expiry_date = null;
        $card->approved_by = null;
        $card->approved_at = null;

        $card->save();

        return $card;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء طلب إصدار البطاقة الشخصية بنجاح';
    }
}
