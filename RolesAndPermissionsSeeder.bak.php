<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Citizens
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'citizens.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
            ],
            [
                'name' => 'citizens.create',
                'name_ar' => 'ط¥ط¶ط§ظپط© ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'create',
                'description' => 'ط¥ط¶ط§ظپط© ظ…ظˆط§ط·ظ† ط¬ط¯ظٹط¯',
            ],
            [
                'name' => 'citizens.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
            ],
            [
                'name' => 'citizens.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
            ],
            [
                'name' => 'citizens.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ† ط§ظ„ظ…ط­ط°ظˆظپظٹظ†',
            ],
            [
                'name' => 'citizens.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ† ظ†ظ‡ط§ط¦ظٹط§ظ‹',
            ],
            [
                'name' => 'citizens.approve',
                'name_ar' => 'ط§ط¹طھظ…ط§ط¯ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'approve',
                'description' => 'ط§ط¹طھظ…ط§ط¯ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ† ط¨ط¹ط¯ ظ…ط±ط§ط¬ط¹طھظ‡ط§',
            ],
            [
                'name' => 'citizens.reject',
                'name_ar' => 'ط±ظپط¶ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'reject',
                'description' => 'ط±ظپط¶ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ† ظ…ط¹ طھظˆط¶ظٹط­ ط§ظ„ط³ط¨ط¨',
            ],
            [
                'name' => 'citizens.resubmit',
                'name_ar' => 'ط¥ط¹ط§ط¯ط© طھظ‚ط¯ظٹظ… ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ†',
                'module' => 'citizens',
                'action' => 'resubmit',
                'description' => 'ط¥ط¹ط§ط¯ط© طھظ‚ط¯ظٹظ… ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط§ط·ظ†ظٹظ† ظ„ظ„ظ…ط±ط§ط¬ط¹ط©',
            ],
            /*
            |--------------------------------------------------------------------------
            | Passports
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'passports.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط¬ظˆط§ط²ط§طھ',
            ],
            [
                'name' => 'passports.create',
                'name_ar' => 'ط¥ط¶ط§ظپط© ط§ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'create',
                'description' => 'ط¥ط¶ط§ظپط© ط¬ظˆط§ط² ط¬ط¯ظٹط¯',
            ],
            [
                'name' => 'passports.edit',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'edit',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط¬ظˆط§ط²ط§طھ',
            ],
            [
                'name' => 'passports.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط¬ظˆط§ط²ط§طھ',
            ],
            [
                'name' => 'passports.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط¬ظˆط§ط²ط§طھ ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],
            [
                'name' => 'passports.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„ط¬ظˆط§ط²ط§طھ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ط§ظ„ظ†ط¸ط§ظ…',
            ],
            [
                'name' => 'passports.approve',
                'name_ar' => 'ط§ط¹طھظ…ط§ط¯ ط§ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'approve',
                'description' => 'ط§ط¹طھظ…ط§ط¯ ط·ظ„ط¨ط§طھ ط§ظ„ط¬ظˆط§ط²ط§طھ',
            ],
            [
                'name' => 'passports.reject',
                'name_ar' => 'ط±ظپط¶ ط§ظ„ط¬ظˆط§ط²ط§طھ',
                'module' => 'passports',
                'action' => 'reject',
                'description' => 'ط±ظپط¶ ط·ظ„ط¨ط§طھ ط§ظ„ط¬ظˆط§ط²ط§طھ',
            ],

            /*
|--------------------------------------------------------------------------
| Identity Cards
|--------------------------------------------------------------------------
*/

            [
                'name' => 'identity_cards.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
            ],
            [
                'name' => 'identity_cards.create',
                'name_ar' => 'ط¥طµط¯ط§ط± ط¨ط·ط§ظ‚ط© ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'create',
                'description' => 'ط¥طµط¯ط§ط± ط¨ط·ط§ظ‚ط© ط´ط®طµظٹط© ط¬ط¯ظٹط¯ط©',
            ],
            [
                'name' => 'identity_cards.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
            ],
            [
                'name' => 'identity_cards.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
            ],
            [
                'name' => 'identity_cards.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط© ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],
            [
                'name' => 'identity_cards.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط© ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ط§ظ„ظ†ط¸ط§ظ…',
            ],
            [
                'name' => 'identity_cards.approve',
                'name_ar' => 'ط§ط¹طھظ…ط§ط¯ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'approve',
                'description' => 'ط§ط¹طھظ…ط§ط¯ ط·ظ„ط¨ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
            ],
            [
                'name' => 'identity_cards.reject',
                'name_ar' => 'ط±ظپط¶ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'reject',
                'description' => 'ط±ظپط¶ ط·ظ„ط¨ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
            ],
            [
                'name' => 'identity_cards.resubmit',
                'name_ar' => 'ط¥ط¹ط§ط¯ط© طھظ‚ط¯ظٹظ… ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'resubmit',
                'description' => 'ط¥ط¹ط§ط¯ط© طھظ‚ط¯ظٹظ… ط·ظ„ط¨ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط© ط§ظ„ظ…ط±ظپظˆط¶ط©',
            ],
            [
                'name' => 'identity_cards.print',
                'name_ar' => 'ط·ط¨ط§ط¹ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط©',
                'module' => 'identity_cards',
                'action' => 'print',
                'description' => 'ط·ط¨ط§ط¹ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط´ط®طµظٹط© ط¨طµظٹط؛ط© PDF',
            ],

            /*
|--------------------------------------------------------------------------
| Family Cards
|--------------------------------------------------------------------------
*/

            [
                'name' => 'family_cards.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
            ],
            [
                'name' => 'family_cards.create',
                'name_ar' => 'ط¥طµط¯ط§ط± ط¨ط·ط§ظ‚ط© ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ط·ظ„ط¨ ط¥طµط¯ط§ط± ط¨ط·ط§ظ‚ط© ط¹ط§ط¦ظ„ظٹط© ط¬ط¯ظٹط¯ط©',
            ],
            [
                'name' => 'family_cards.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
            ],
            [
                'name' => 'family_cards.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط© ظˆظ†ظ‚ظ„ظ‡ط§ ط¥ظ„ظ‰ ط³ظ„ط© ط§ظ„ظ…ط­ط°ظˆظپط§طھ',
            ],
            [
                'name' => 'family_cards.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط© ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],
            [
                'name' => 'family_cards.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط© ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ط§ظ„ظ†ط¸ط§ظ…',
            ],
            [
                'name' => 'family_cards.approve',
                'name_ar' => 'ط§ط¹طھظ…ط§ط¯ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'approve',
                'description' => 'ط§ط¹طھظ…ط§ط¯ ط·ظ„ط¨ط§طھ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط© ظˆطھط­ط¯ظٹط¯ طھظˆط§ط±ظٹط® ط§ظ„ط¥طµط¯ط§ط± ظˆط§ظ„ط§ظ†طھظ‡ط§ط،',
            ],
            [
                'name' => 'family_cards.reject',
                'name_ar' => 'ط±ظپط¶ ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'reject',
                'description' => 'ط±ظپط¶ ط·ظ„ط¨ط§طھ ط¥طµط¯ط§ط± ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
            ],
            [
                'name' => 'family_cards.print',
                'name_ar' => 'ط·ط¨ط§ط¹ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط©',
                'module' => 'family_cards',
                'action' => 'print',
                'description' => 'ط·ط¨ط§ط¹ط© ط§ظ„ط¨ط·ط§ظ‚ط§طھ ط§ظ„ط¹ط§ط¦ظ„ظٹط© ط¨طµظٹط؛ط© PDF ظˆطھط³ط¬ظٹظ„ ط¹ط¯ط¯ ظ…ط±ط§طھ ط§ظ„ط·ط¨ط§ط¹ط©',
            ],

            /*
|--------------------------------------------------------------------------
| Birth Certificates
|--------------------------------------------------------------------------
*/

            [
                'name' => 'birth_certificates.view',
                'name_ar' => 'ط¹ط±ط¶ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯ ظˆطھظپط§طµظٹظ„ ط§ظ„ط´ظ‡ط§ط¯ط§طھ',
            ],
            [
                'name' => 'birth_certificates.create',
                'name_ar' => 'ط¥ظ†ط´ط§ط، ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ط·ظ„ط¨ ط¥طµط¯ط§ط± ط´ظ‡ط§ط¯ط© ظ…ظٹظ„ط§ط¯ ط¬ط¯ظٹط¯ط©',
            ],
            [
                'name' => 'birth_certificates.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
            ],
            [
                'name' => 'birth_certificates.delete',
                'name_ar' => 'ط­ط°ظپ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯ ظˆظ†ظ‚ظ„ظ‡ط§ ط¥ظ„ظ‰ ط³ظ„ط© ط§ظ„ظ…ط­ط°ظˆظپط§طھ',
            ],
            [
                'name' => 'birth_certificates.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯ ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],
            [
                'name' => 'birth_certificates.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ط§ظ„ظ†ط¸ط§ظ…',
            ],
            [
                'name' => 'birth_certificates.approve',
                'name_ar' => 'ط§ط¹طھظ…ط§ط¯ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'approve',
                'description' => 'ط§ط¹طھظ…ط§ط¯ ط·ظ„ط¨ط§طھ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯ ظˆطھط­ط¯ظٹط¯ طھط§ط±ظٹط® ط§ظ„ط¥طµط¯ط§ط±',
            ],
            [
                'name' => 'birth_certificates.print',
                'name_ar' => 'ط·ط¨ط§ط¹ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'print',
                'description' => 'ط·ط¨ط§ط¹ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯ ط¨طµظٹط؛ط© PDF ظˆطھط³ط¬ظٹظ„ ط¹ط¯ط¯ ظ…ط±ط§طھ ط§ظ„ط·ط¨ط§ط¹ط©',
            ],
            [
                'name' => 'birth_certificates.cancel',
                'name_ar' => 'ط¥ظ„ط؛ط§ط، ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظ…ظٹظ„ط§ط¯',
                'module' => 'birth_certificates',
                'action' => 'cancel',
                'description' => 'ط¥ظ„ط؛ط§ط، ط·ظ„ط¨ ط´ظ‡ط§ط¯ط© ط§ظ„ظ…ظٹظ„ط§ط¯ ظˆطھط؛ظٹظٹط± ط­ط§ظ„طھظ‡ ط¥ظ„ظ‰ ظ…ظ„ط؛ط§ط©',
            ],
            /*
            |--------------------------------------------------------------------------
            | Death Certificates
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'death_certificates.view',
                'name_ar' => 'ط¹ط±ط¶ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط© ظˆطھظپط§طµظٹظ„ ط§ظ„ط´ظ‡ط§ط¯ط§طھ',
            ],
            [
                'name' => 'death_certificates.create',
                'name_ar' => 'ط¥ظ†ط´ط§ط، ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ط·ظ„ط¨ ط¥طµط¯ط§ط± ط´ظ‡ط§ط¯ط© ظˆظپط§ط© ط¬ط¯ظٹط¯ط©',
            ],
            [
                'name' => 'death_certificates.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
            ],
            [
                'name' => 'death_certificates.delete',
                'name_ar' => 'ط­ط°ظپ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط© ظˆظ†ظ‚ظ„ظ‡ط§ ط¥ظ„ظ‰ ط³ظ„ط© ط§ظ„ظ…ط­ط°ظˆظپط§طھ',
            ],
            [
                'name' => 'death_certificates.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط© ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],
            [
                'name' => 'death_certificates.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط© ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ط§ظ„ظ†ط¸ط§ظ…',
            ],
            [
                'name' => 'death_certificates.approve',
                'name_ar' => 'ط§ط¹طھظ…ط§ط¯ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'approve',
                'description' => 'ط§ط¹طھظ…ط§ط¯ ط·ظ„ط¨ط§طھ ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط© ظˆطھط­ط¯ظٹط¯ طھط§ط±ظٹط® ط§ظ„ط¥طµط¯ط§ط±',
            ],
            [
                'name' => 'death_certificates.print',
                'name_ar' => 'ط·ط¨ط§ط¹ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'print',
                'description' => 'ط·ط¨ط§ط¹ط© ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط© ط¨طµظٹط؛ط© PDF ظˆطھط³ط¬ظٹظ„ ط¹ط¯ط¯ ظ…ط±ط§طھ ط§ظ„ط·ط¨ط§ط¹ط©',
            ],
            [
                'name' => 'death_certificates.cancel',
                'name_ar' => 'ط¥ظ„ط؛ط§ط، ط´ظ‡ط§ط¯ط§طھ ط§ظ„ظˆظپط§ط©',
                'module' => 'death_certificates',
                'action' => 'cancel',
                'description' => 'ط¥ظ„ط؛ط§ط، ط·ظ„ط¨ ط´ظ‡ط§ط¯ط© ط§ظ„ظˆظپط§ط© ظˆطھط؛ظٹظٹط± ط­ط§ظ„طھظ‡ ط¥ظ„ظ‰ ظ…ظ„ط؛ط§ط©',
            ],
            [
                'name' => 'appointments.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ظ…ظˆط§ط¹ظٹط¯',
                'module' => 'appointments',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„ظ…ظˆط§ط¹ظٹط¯ ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],

            [
                'name' => 'appointments.create',
                'name_ar' => 'ط­ط¬ط² ظ…ظˆط¹ط¯',
                'module' => 'appointments',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ظ…ظˆط¹ط¯ ط¬ط¯ظٹط¯ ظˆط­ط¬ط² ظ…ظˆط¹ط¯ ظ„ظ„ظ…ظˆط§ط·ظ†',
            ],

            [
                'name' => 'appointments.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ظ…ظˆط§ط¹ظٹط¯',
                'module' => 'appointments',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ظˆط¹ط¯',
            ],

            [
                'name' => 'appointments.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ظ…ظˆط§ط¹ظٹط¯',
                'module' => 'appointments',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ…ظˆط§ط¹ظٹط¯',
            ],

            [
                'name' => 'appointments.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ظˆط§ط¹ظٹط¯',
                'module' => 'appointments',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ظˆط§ط¹ظٹط¯ ط§ظ„ظ…ط­ط°ظˆظپط© soft delete',
            ],

            [
                'name' => 'appointments.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ظ…ظˆط§ط¹ظٹط¯',
                'module' => 'appointments',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ…ظˆط§ط¹ظٹط¯ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ',
            ],

            [
                'name' => 'appointments.confirm',
                'name_ar' => 'طھط£ظƒظٹط¯ ط§ظ„ظ…ظˆط§ط¹ظٹط¯',
                'module' => 'appointments',
                'action' => 'confirm',
                'description' => 'طھط£ظƒظٹط¯ ط§ظ„ظ…ظˆط¹ط¯ ظˆطھط³ط¬ظٹظ„ ط§ظ„ظ…ط³طھط®ط¯ظ… ط§ظ„ط°ظٹ ط£ظƒط¯ظ‡ ظˆظˆظ‚طھ ط§ظ„طھط£ظƒظٹط¯',
            ],

            [
                'name' => 'appointments.attend',
                'name_ar' => 'طھط³ط¬ظٹظ„ ط§ظ„ط­ط¶ظˆط±',
                'module' => 'appointments',
                'action' => 'attend',
                'description' => 'طھط³ط¬ظٹظ„ ط­ط¶ظˆط± ط§ظ„ظ…ظˆط§ط·ظ† ظ„ظ„ظ…ظˆط¹ط¯',
            ],

            [
                'name' => 'appointments.no_show',
                'name_ar' => 'طھط³ط¬ظٹظ„ ط¹ط¯ظ… ط§ظ„ط­ط¶ظˆط±',
                'module' => 'appointments',
                'action' => 'no_show',
                'description' => 'طھط³ط¬ظٹظ„ ط¹ط¯ظ… ط­ط¶ظˆط± ط§ظ„ظ…ظˆط§ط·ظ† ظ„ظ„ظ…ظˆط¹ط¯',
            ],

            [
                'name' => 'appointments.cancel',
                'name_ar' => 'ط¥ظ„ط؛ط§ط، ط§ظ„ظ…ظˆط§ط¹ظٹط¯',
                'module' => 'appointments',
                'action' => 'cancel',
                'description' => 'ط¥ظ„ط؛ط§ط، ط§ظ„ظ…ظˆط¹ط¯ ط¥ط°ط§ ظƒط§ظ†طھ ط­ط§ظ„طھظ‡ ظ‚ظٹط¯ ط§ظ„ط§ظ†طھط¸ط§ط± ط£ظˆ ظ…ط¤ظƒط¯ظ‹ط§',
            ],
            [
                'name' => 'branches.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ظپط±ظˆط¹',
                'module' => 'branches',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„ظپط±ظˆط¹ ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],

            [
                'name' => 'branches.create',
                'name_ar' => 'ط¥ط¶ط§ظپط© ظپط±ط¹',
                'module' => 'branches',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ظپط±ط¹ ط¬ط¯ظٹط¯ ظپظٹ ط§ظ„ظ†ط¸ط§ظ…',
            ],

            [
                'name' => 'branches.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ظپط±ظˆط¹',
                'module' => 'branches',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظپط±ظˆط¹ ط§ظ„ظ…ظˆط¬ظˆط¯ط©',
            ],

            [
                'name' => 'branches.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ظپط±ظˆط¹',
                'module' => 'branches',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظپط±ظˆط¹ ط؛ظٹط± ط§ظ„ظ…ط±طھط¨ط·ط© ط¨ظ…ط³طھط®ط¯ظ…ظٹظ†',
            ],

            [
                'name' => 'branches.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظپط±ظˆط¹',
                'module' => 'branches',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظپط±ظˆط¹ ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],

            [
                'name' => 'branches.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ظپط±ظˆط¹',
                'module' => 'branches',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظپط±ظˆط¹ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ',
            ],


            [
                'name' => 'districts.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ',
                'module' => 'districts',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],

            [
                'name' => 'districts.create',
                'name_ar' => 'ط¥ط¶ط§ظپط© ظ…ط¯ظٹط±ظٹط©',
                'module' => 'districts',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ظ…ط¯ظٹط±ظٹط© ط¬ط¯ظٹط¯ط© ظˆط±ط¨ط·ظ‡ط§ ط¨ظ…ط­ط§ظپط¸ط©',
            ],

            [
                'name' => 'districts.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ',
                'module' => 'districts',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ ط§ظ„ظ…ظˆط¬ظˆط¯ط©',
            ],

            [
                'name' => 'districts.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ',
                'module' => 'districts',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ',
            ],

            [
                'name' => 'districts.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ',
                'module' => 'districts',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],

            [
                'name' => 'districts.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ظ…ط¯ظٹط±ظٹط§طھ',
                'module' => 'districts',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ…ط¯ظٹط±ظٹط§طھ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ',
            ],

            [
                'name' => 'governorates.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ظ…ط­ط§ظپط¸ط§طھ',
                'module' => 'governorates',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„ظ…ط­ط§ظپط¸ط§طھ ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],

            [
                'name' => 'governorates.create',
                'name_ar' => 'ط¥ظ†ط´ط§ط، ظ…ط­ط§ظپط¸ط©',
                'module' => 'governorates',
                'action' => 'create',
                'description' => 'ط¥ط¶ط§ظپط© ظ…ط­ط§ظپط¸ط© ط¬ط¯ظٹط¯ط© ط¥ظ„ظ‰ ط§ظ„ظ†ط¸ط§ظ…',
            ],

            [
                'name' => 'governorates.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ظ…ط­ط§ظپط¸ط§طھ',
                'module' => 'governorates',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ط­ط§ظپط¸ط§طھ ط§ظ„ظ…ط³ط¬ظ„ط©',
            ],

            [
                'name' => 'governorates.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ظ…ط­ط§ظپط¸ط§طھ',
                'module' => 'governorates',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ…ط­ط§ظپط¸ط§طھ ط­ط°ظپظ‹ط§ ط¹ط§ط¯ظٹظ‹ط§',
            ],

            [
                'name' => 'governorates.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ط­ط§ظپط¸ط§طھ',
                'module' => 'governorates',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ظ…ط­ط§ظپط¸ط§طھ ط§ظ„ظ…ط­ط°ظˆظپط© ط­ط°ظپظ‹ط§ ظ†ط§ط¹ظ…ظ‹ط§',
            ],

            [
                'name' => 'governorates.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ظ…ط­ط§ظپط¸ط§طھ',
                'module' => 'governorates',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ…ط­ط§ظپط¸ط§طھ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ',
            ],
            [
                'name' => 'service_requests.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„ط·ظ„ط¨ط§طھ ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],

            [
                'name' => 'service_requests.create',
                'name_ar' => 'ط¥ظ†ط´ط§ط، ط§ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ط·ظ„ط¨ ط®ط¯ظ…ط© ط¬ط¯ظٹط¯',
            ],

            [
                'name' => 'service_requests.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط·ظ„ط¨ط§طھ ط§ظ„ط®ط¯ظ…ط§طھ',
            ],

            [
                'name' => 'service_requests.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط·ظ„ط¨ط§طھ ط§ظ„ط®ط¯ظ…ط§طھ ط­ط°ظپظ‹ط§ ط¹ط§ط¯ظٹظ‹ط§',
            ],

            [
                'name' => 'service_requests.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط·ظ„ط¨ط§طھ ط§ظ„ط®ط¯ظ…ط§طھ ط§ظ„ظ…ط­ط°ظˆظپط© ط­ط°ظپظ‹ط§ ظ†ط§ط¹ظ…ظ‹ط§',
            ],

            [
                'name' => 'service_requests.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط·ظ„ط¨ط§طھ ط§ظ„ط®ط¯ظ…ط§طھ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ',
            ],

            [
                'name' => 'service_requests.approve',
                'name_ar' => 'ط§ظ„ظ…ظˆط§ظپظ‚ط© ط¹ظ„ظ‰ ط§ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'approve',
                'description' => 'ط§ط¹طھظ…ط§ط¯ ط·ظ„ط¨ط§طھ ط§ظ„ط®ط¯ظ…ط§طھ ظˆط¥ظ†ط´ط§ط، ط¨ط·ط§ظ‚ط© ط§ظ„ظ‡ظˆظٹط© ط¹ظ†ط¯ ط§ظ†ط·ط¨ط§ظ‚ ظ†ظˆط¹ ط§ظ„ط®ط¯ظ…ط©',
            ],

            [
                'name' => 'service_requests.reject',
                'name_ar' => 'ط±ظپط¶ ط§ظ„ط·ظ„ط¨ط§طھ',
                'module' => 'service_requests',
                'action' => 'reject',
                'description' => 'ط±ظپط¶ ط·ظ„ط¨ط§طھ ط§ظ„ط®ط¯ظ…ط§طھ ظ…ط¹ طھط³ط¬ظٹظ„ ط³ط¨ط¨ ط§ظ„ط±ظپط¶',
            ],


            [
                'name' => 'service_fees.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ط±ط³ظˆظ…',
                'module' => 'service_fees',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط±ط³ظˆظ… ط§ظ„ط®ط¯ظ…ط§طھ ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],

            [
                'name' => 'service_fees.create',
                'name_ar' => 'ط¥ظ†ط´ط§ط، ط§ظ„ط±ط³ظˆظ…',
                'module' => 'service_fees',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ط±ط³ظˆظ… ط¬ط¯ظٹط¯ط© ظ„ظ„ط®ط¯ظ…ط§طھ',
            ],

            [
                'name' => 'service_fees.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ط±ط³ظˆظ…',
                'module' => 'service_fees',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط±ط³ظˆظ… ط§ظ„ط®ط¯ظ…ط§طھ',
            ],

            [
                'name' => 'service_fees.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ط±ط³ظˆظ…',
                'module' => 'service_fees',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط±ط³ظˆظ… ط§ظ„ط®ط¯ظ…ط§طھ ط­ط°ظپظ‹ط§ ط¹ط§ط¯ظٹظ‹ط§',
            ],

            [
                'name' => 'service_fees.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„ط±ط³ظˆظ…',
                'module' => 'service_fees',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط±ط³ظˆظ… ط§ظ„ط®ط¯ظ…ط§طھ ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],

            [
                'name' => 'service_fees.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„ط±ط³ظˆظ…',
                'module' => 'service_fees',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط±ط³ظˆظ… ط§ظ„ط®ط¯ظ…ط§طھ ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ',
            ],

            [
                'name' => 'reports.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„طھظ‚ط§ط±ظٹط±',
                'module' => 'reports',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„طھظ‚ط§ط±ظٹط± ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],

            [
                'name' => 'reports.create',
                'name_ar' => 'ط¥ظ†ط´ط§ط، ط§ظ„طھظ‚ط§ط±ظٹط±',
                'module' => 'reports',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ط§ظ„طھظ‚ط§ط±ظٹط± ظˆطھظˆظ„ظٹط¯ ظ…ظ„ظپط§طھظ‡ط§',
            ],

            [
                'name' => 'reports.update',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„طھظ‚ط§ط±ظٹط±',
                'module' => 'reports',
                'action' => 'update',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„طھظ‚ط§ط±ظٹط±',
            ],

            [
                'name' => 'reports.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„طھظ‚ط§ط±ظٹط±',
                'module' => 'reports',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„طھظ‚ط§ط±ظٹط± ط­ط°ظپظ‹ط§ ط¹ط§ط¯ظٹظ‹ط§',
            ],

            [
                'name' => 'reports.restore',
                'name_ar' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„طھظ‚ط§ط±ظٹط±',
                'module' => 'reports',
                'action' => 'restore',
                'description' => 'ط§ط³طھط¹ط§ط¯ط© ط§ظ„طھظ‚ط§ط±ظٹط± ط§ظ„ظ…ط­ط°ظˆظپط©',
            ],

            [
                'name' => 'reports.force_delete',
                'name_ar' => 'ط§ظ„ط­ط°ظپ ط§ظ„ظ†ظ‡ط§ط¦ظٹ ظ„ظ„طھظ‚ط§ط±ظٹط±',
                'module' => 'reports',
                'action' => 'force_delete',
                'description' => 'ط­ط°ظپ ط§ظ„طھظ‚ط§ط±ظٹط± ظ†ظ‡ط§ط¦ظٹظ‹ط§ ظ…ظ† ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ',
            ],

            [
                'name' => 'reports.download',
                'name_ar' => 'طھط­ظ…ظٹظ„ ط§ظ„طھظ‚ط§ط±ظٹط±',
                'module' => 'reports',
                'action' => 'download',
                'description' => 'طھط­ظ…ظٹظ„ ظ…ظ„ظپط§طھ ط§ظ„طھظ‚ط§ط±ظٹط± ط§ظ„ظ…ظƒطھظ…ظ„ط©',
            ],

            [
                'name' => 'backups.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹط©',
                'module' => 'backups',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹط© ظˆطھظپط§طµظٹظ„ظ‡ط§',
            ],
            [
                'name' => 'backups.create',
                'name_ar' => 'ط¥ظ†ط´ط§ط، ظ†ط³ط®ط© ط§ط­طھظٹط§ط·ظٹط©',
                'module' => 'backups',
                'action' => 'create',
                'description' => 'ط¥ظ†ط´ط§ط، ظ†ط³ط®ط© ط§ط­طھظٹط§ط·ظٹط© ظٹط¯ظˆظٹط© ظ„ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ ظˆظ…ظ„ظپط§طھ ط§ظ„ظ†ط¸ط§ظ…',
            ],
            [
                'name' => 'backups.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹط©',
                'module' => 'backups',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹط© ظˆظ…ظ„ظپط§طھظ‡ط§ ظˆط³ط¬ظ„ط§طھظ‡ط§',
            ],
            [
                'name' => 'backups.download',
                'name_ar' => 'طھظ†ط²ظٹظ„ ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹط©',
                'module' => 'backups',
                'action' => 'download',
                'description' => 'طھظ†ط²ظٹظ„ ظ…ظ„ظپط§طھ ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹط© ط§ظ„ظ…ظƒطھظ…ظ„ط©',
            ],


            [
                'name' => 'audit_logs.view',
                'name_ar' => 'ط¹ط±ط¶ ط³ط¬ظ„ ط§ظ„طھط¯ظ‚ظٹظ‚',
                'module' => 'audit_logs',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ظ‚ط§ط¦ظ…ط© ط³ط¬ظ„ط§طھ ط§ظ„طھط¯ظ‚ظٹظ‚ ظˆطھظپط§طµظٹظ„ ط§ظ„ط¹ظ…ظ„ظٹط§طھ ط§ظ„طھظٹ طھظ…طھ ط¯ط§ط®ظ„ ط§ظ„ظ†ط¸ط§ظ…',
            ],


            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'users.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†',
                'module' => 'users',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†',
            ],
            [
                'name' => 'users.create',
                'name_ar' => 'ط¥ط¶ط§ظپط© ط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†',
                'module' => 'users',
                'action' => 'create',
                'description' => 'ط¥ط¶ط§ظپط© ظ…ط³طھط®ط¯ظ… ط¬ط¯ظٹط¯',
            ],
            [
                'name' => 'users.edit',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†',
                'module' => 'users',
                'action' => 'edit',
                'description' => 'طھط¹ط¯ظٹظ„ ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†',
            ],
            [
                'name' => 'users.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†',
                'module' => 'users',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ظ…ط³طھط®ط¯ظ…ظٹظ†',
            ],

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'roles.view',
                'name_ar' => 'ط¹ط±ط¶ ط§ظ„ط£ط¯ظˆط§ط±',
                'module' => 'roles',
                'action' => 'view',
                'description' => 'ط¹ط±ط¶ ط§ظ„ط£ط¯ظˆط§ط± ظˆط§ظ„طµظ„ط§ط­ظٹط§طھ',
            ],
            [
                'name' => 'roles.create',
                'name_ar' => 'ط¥ط¶ط§ظپط© ط§ظ„ط£ط¯ظˆط§ط±',
                'module' => 'roles',
                'action' => 'create',
                'description' => 'ط¥ط¶ط§ظپط© ط¯ظˆط± ط¬ط¯ظٹط¯',
            ],
            [
                'name' => 'roles.edit',
                'name_ar' => 'طھط¹ط¯ظٹظ„ ط§ظ„ط£ط¯ظˆط§ط±',
                'module' => 'roles',
                'action' => 'edit',
                'description' => 'طھط¹ط¯ظٹظ„ ط§ظ„ط£ط¯ظˆط§ط± ظˆط§ظ„طµظ„ط§ط­ظٹط§طھ',
            ],
            [
                'name' => 'roles.delete',
                'name_ar' => 'ط­ط°ظپ ط§ظ„ط£ط¯ظˆط§ط±',
                'module' => 'roles',
                'action' => 'delete',
                'description' => 'ط­ط°ظپ ط§ظ„ط£ط¯ظˆط§ط±',
            ],
            [
                'name' => 'settings.backup',
                'name_ar' => 'ط¥ط¯ط§ط±ط© ط¥ط¹ط¯ط§ط¯ط§طھ ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹ',
                'module' => 'settings',
                'action' => 'backup',
                'description' => 'ط¥ط¯ط§ط±ط© ط¥ط¹ط¯ط§ط¯ط§طھ ط§ظ„ظ†ط³ط® ط§ظ„ط§ط­طھظٹط§ط·ظٹ ط§ظ„طھظ„ظ‚ط§ط¦ظٹ',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                [
                    'name' => $permissionData['name'],
                    'guard_name' => 'web',
                ],
                [
                    'id' => (string) Str::uuid(),
                    'name_ar' => $permissionData['name_ar'],
                    'module' => $permissionData['module'],
                    'action' => $permissionData['action'],
                    'description' => $permissionData['description'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [
            [
                'name' => 'ظ…ط¯ظٹط± ط§ظ„ظ†ط¸ط§ظ…',
            ],
            [
                'name' => 'ظ…ظˆط¸ظپ ط§ظ„طھط³ط¬ظٹظ„',
            ],
            [
                'name' => 'ظ…ظˆط¸ظپ ط§ظ„ط¬ظˆط§ط²ط§طھ',
            ],
            [
                'name' => 'ط§ظ„ظ…ط¹طھظ…ط¯',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Roles
        |--------------------------------------------------------------------------
        */

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                [
                    'name' => $roleData['name'],
                    'guard_name' => 'web',
                ],
                [
                    'id' => (string) Str::uuid(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::where('name', 'ظ…ط¯ظٹط± ط§ظ„ظ†ط¸ط§ظ…')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $registrationOfficer = Role::where('name', 'ظ…ظˆط¸ظپ ط§ظ„طھط³ط¬ظٹظ„')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $passportOfficer = Role::where('name', 'ظ…ظˆط¸ظپ ط§ظ„ط¬ظˆط§ط²ط§طھ')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $approver = Role::where('name', 'ط§ظ„ظ…ط¹طھظ…ط¯')
            ->where('guard_name', 'web')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */

        // Admin
        // ظٹط­طµظ„ ط¹ظ„ظ‰ ط¬ظ…ظٹط¹ ط§ظ„طµظ„ط§ط­ظٹط§طھ ظپظٹ ط§ظ„ظ†ط¸ط§ظ…
        $admin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // Registration Officer
        $registrationOfficer->syncPermissions([
            'citizens.view',
            'citizens.create',
            'citizens.update',
        ]);

        // Passport Officer
        $passportOfficer->syncPermissions([
            'citizens.view',
            'passports.view',
            'passports.create',
            'passports.edit',
        ]);

        // Approver
        $approver->syncPermissions([
            'citizens.view',
            'passports.view',
            'passports.approve',
            'passports.reject',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

