<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Permission;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('roles.create'),
            403
        );

        parent::mount();
    }

    protected function getSelectedPermissionIds(): array
    {
        $permissionIds = [];

        foreach ($this->data as $key => $value) {
            if (
                str_starts_with($key, 'permissions_') &&
                is_array($value)
            ) {
                $permissionIds = array_merge($permissionIds, $value);
            }
        }

        return array_values(array_unique($permissionIds));
    }

    protected function handleRecordCreation(array $data): Model
    {
        abort_unless(
            auth()->user()?->can('roles.create'),
            403
        );

        $permissionIds = $this->getSelectedPermissionIds();

        if (empty($permissionIds)) {
            throw ValidationException::withMessages([
                'data.permissions_users' =>
                    'يجب اختيار صلاحية واحدة على الأقل قبل إضافة الدور.',
            ]);
        }

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('id', $permissionIds)
            ->get();

        if ($permissions->isEmpty()) {
            throw ValidationException::withMessages([
                'data.permissions_users' =>
                    'لم يتم العثور على صلاحية صحيحة. يرجى اختيار صلاحية أخرى.',
            ]);
        }

        return DB::transaction(function () use ($data, $permissions): Model {
            $role = static::getModel()::create($data);

            $role->syncPermissions($permissions);

            return $role;
        });
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('index');
    }
}
