<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFamilyCard extends EditRecord
{
    protected static string $resource = FamilyCardResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('family_cards.update'),
            403
        );

        parent::mount($record);
    }

    public function getTitle(): string
    {
        return 'تعديل البطاقة العائلية';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset(
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
                ->label('عرض')
                ->icon('heroicon-o-eye')
                ->visible(
                    fn (): bool => auth()->user()?->can('family_cards.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف البطاقة')
                ->visible(
                    fn (): bool => auth()->user()?->can('family_cards.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->label('حذف نهائي')
                ->visible(
                    fn (): bool => auth()->user()?->can('family_cards.force_delete') ?? false
                ),

            RestoreAction::make()
                ->label('استعادة البطاقة')
                ->visible(
                    fn (): bool => auth()->user()?->can('family_cards.restore') ?? false
                ),
        ];
    }
}
