<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Permission;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('roles.update'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض الدور')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('roles.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف الدور')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف الدور')
                ->modalDescription('هل أنت متأكد من حذف هذا الدور؟')
                ->modalSubmitActionLabel('نعم، حذف')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('roles.delete') ?? false
                )
                ->before(function (): void {
                    abort_unless(
                        auth()->user()?->can('roles.delete'),
                        403
                    );
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($this->getModules() as $module) {
            $data["permissions_{$module}"] = $this->record
                ->permissions()
                ->where('module', $module)
                ->where('guard_name', 'web')
                ->pluck('permissions.id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        }

        return $data;
    }

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {
        abort_unless(
            auth()->user()?->can('roles.update'),
            403
        );

        $permissionIds = [];

        foreach (($this->data ?? []) as $key => $value) {
            if (
                str_starts_with($key, 'permissions_') &&
                is_array($value)
            ) {
                $permissionIds = array_merge(
                    $permissionIds,
                    $value
                );
            }
        }

        $permissionIds = array_values(
            array_unique(
                array_map('strval', $permissionIds)
            )
        );

        if (empty($permissionIds)) {
            throw ValidationException::withMessages([
                'data.permissions_users' =>
                    'يجب تحديد صلاحية واحدة على الأقل للدور.',
            ]);
        }

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('id', $permissionIds)
            ->get();

        if ($permissions->count() !== count($permissionIds)) {
            throw ValidationException::withMessages([
                'data.permissions_users' =>
                    'توجد صلاحيات غير صالحة ضمن الاختيارات.',
            ]);
        }

        foreach (array_keys($data) as $key) {
            if (str_starts_with($key, 'permissions_')) {
                unset($data[$key]);
            }
        }

        return DB::transaction(function () use (
            $record,
            $data,
            $permissions
        ): Model {
            $record->update($data);

            $record->syncPermissions($permissions);

            return $record;
        });
    }

    protected function getModules(): array
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module')
            ->toArray();
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('view', [
            'record' => $this->getRecord(),
        ]);
    }
}
