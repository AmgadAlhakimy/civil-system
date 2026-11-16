<?php
namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Roles\Schemas\RoleInfolist;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('roles.view'),
            403
        );

        parent::mount($record);
    }

    public function infolist(Schema $schema): Schema
    {
        return RoleInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل الدور')
                ->icon('heroicon-o-pencil-square')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('roles.update') ?? false
                ),

            DeleteAction::make()
                ->label('حذف الدور نهائياً')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف الدور')
                ->modalDescription('هل أنت متأكد من حذف هذا الدور نهائياً؟')
                ->modalSubmitActionLabel('نعم، حذف')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('roles.delete') ?? false
                )
                ->before(function (DeleteAction $action): void {
                    abort_unless(
                        auth()->user()?->can('roles.delete'),
                        403
                    );

                    $role = $this->getRecord();

                    $usersCount = $role->users()->count();

                    if ($usersCount > 0) {
                        Notification::make()
                            ->title('تعذر حذف الدور')
                            ->body(
                                "لا يمكن حذف الدور ({$role->name}) لأنه مرتبط بـ {$usersCount} مستخدم."
                            )
                            ->warning()
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
