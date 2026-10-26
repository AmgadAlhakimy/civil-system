<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIdentityCard extends EditRecord
{
    protected static string $resource = IdentityCardResource::class;

    public function getTitle(): string
    {
        return 'تعديل البطاقة الشخصية';
    }

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('identity_cards.update'),
            403
        );

        parent::mount($record);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset(
            $data['national_id'],
            $data['issued_by'],
            $data['issue_date'],
            $data['expiry_date'],
            $data['approved_by'],
            $data['approved_at'],
            $data['print_count'],
            $data['status']
        );

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض البطاقة')
                ->icon('heroicon-o-eye')
                ->visible(
                    fn (): bool => auth()->user()?->can('identity_cards.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف البطاقة')
                ->icon('heroicon-o-trash')
                ->visible(
                    fn (): bool => auth()->user()?->can('identity_cards.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->label('حذف نهائي')
                ->visible(
                    fn (): bool => auth()->user()?->can('identity_cards.force_delete') ?? false
                ),

            RestoreAction::make()
                ->label('استعادة البطاقة')
                ->visible(
                    fn (): bool => auth()->user()?->can('identity_cards.restore') ?? false
                ),
        ];
    }
}
