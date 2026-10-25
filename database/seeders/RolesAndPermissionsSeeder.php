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
                'name_ar' => 'عرض المواطنين',
                'module' => 'citizens',
                'action' => 'view',
                'description' => 'عرض بيانات المواطنين',
            ],
            [
                'name' => 'citizens.create',
                'name_ar' => 'إضافة المواطنين',
                'module' => 'citizens',
                'action' => 'create',
                'description' => 'إضافة مواطن جديد',
            ],
            [
                'name' => 'citizens.update',
                'name_ar' => 'تعديل المواطنين',
                'module' => 'citizens',
                'action' => 'update',
                'description' => 'تعديل بيانات المواطنين',
            ],
            [
                'name' => 'citizens.delete',
                'name_ar' => 'حذف المواطنين',
                'module' => 'citizens',
                'action' => 'delete',
                'description' => 'حذف بيانات المواطنين',
            ],
            [
                'name' => 'citizens.restore',
                'name_ar' => 'استعادة المواطنين',
                'module' => 'citizens',
                'action' => 'restore',
                'description' => 'استعادة المواطنين المحذوفين',
            ],
            [
                'name' => 'citizens.force_delete',
                'name_ar' => 'الحذف النهائي للمواطنين',
                'module' => 'citizens',
                'action' => 'force_delete',
                'description' => 'حذف بيانات المواطنين نهائياً',
            ],
            [
                'name' => 'citizens.approve',
                'name_ar' => 'اعتماد بيانات المواطنين',
                'module' => 'citizens',
                'action' => 'approve',
                'description' => 'اعتماد بيانات المواطنين بعد مراجعتها',
            ],
            [
                'name' => 'citizens.reject',
                'name_ar' => 'رفض بيانات المواطنين',
                'module' => 'citizens',
                'action' => 'reject',
                'description' => 'رفض بيانات المواطنين مع توضيح السبب',
            ],
            [
                'name' => 'citizens.resubmit',
                'name_ar' => 'إعادة تقديم بيانات المواطنين',
                'module' => 'citizens',
                'action' => 'resubmit',
                'description' => 'إعادة تقديم بيانات المواطنين للمراجعة',
            ],
            /*
            |--------------------------------------------------------------------------
            | Passports
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'passports.view',
                'name_ar' => 'عرض الجوازات',
                'module' => 'passports',
                'action' => 'view',
                'description' => 'عرض بيانات الجوازات',
            ],
            [
                'name' => 'passports.create',
                'name_ar' => 'إضافة الجوازات',
                'module' => 'passports',
                'action' => 'create',
                'description' => 'إضافة جواز جديد',
            ],
            [
                'name' => 'passports.edit',
                'name_ar' => 'تعديل الجوازات',
                'module' => 'passports',
                'action' => 'edit',
                'description' => 'تعديل بيانات الجوازات',
            ],
            [
                'name' => 'passports.delete',
                'name_ar' => 'حذف الجوازات',
                'module' => 'passports',
                'action' => 'delete',
                'description' => 'حذف بيانات الجوازات',
            ],
            [
                'name' => 'passports.restore',
                'name_ar' => 'استعادة الجوازات',
                'module' => 'passports',
                'action' => 'restore',
                'description' => 'استعادة الجوازات المحذوفة',
            ],
            [
                'name' => 'passports.force_delete',
                'name_ar' => 'الحذف النهائي للجوازات',
                'module' => 'passports',
                'action' => 'force_delete',
                'description' => 'حذف الجوازات نهائيًا من النظام',
            ],
            [
                'name' => 'passports.approve',
                'name_ar' => 'اعتماد الجوازات',
                'module' => 'passports',
                'action' => 'approve',
                'description' => 'اعتماد طلبات الجوازات',
            ],
            [
                'name' => 'passports.reject',
                'name_ar' => 'رفض الجوازات',
                'module' => 'passports',
                'action' => 'reject',
                'description' => 'رفض طلبات الجوازات',
            ],

            /*
|--------------------------------------------------------------------------
| Identity Cards
|--------------------------------------------------------------------------
*/

            [
                'name' => 'identity_cards.view',
                'name_ar' => 'عرض البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'view',
                'description' => 'عرض بيانات البطاقات الشخصية',
            ],
            [
                'name' => 'identity_cards.create',
                'name_ar' => 'إصدار بطاقة شخصية',
                'module' => 'identity_cards',
                'action' => 'create',
                'description' => 'إصدار بطاقة شخصية جديدة',
            ],
            [
                'name' => 'identity_cards.update',
                'name_ar' => 'تعديل البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'update',
                'description' => 'تعديل بيانات البطاقات الشخصية',
            ],
            [
                'name' => 'identity_cards.delete',
                'name_ar' => 'حذف البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'delete',
                'description' => 'حذف البطاقات الشخصية',
            ],
            [
                'name' => 'identity_cards.restore',
                'name_ar' => 'استعادة البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'restore',
                'description' => 'استعادة البطاقات الشخصية المحذوفة',
            ],
            [
                'name' => 'identity_cards.force_delete',
                'name_ar' => 'الحذف النهائي للبطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'force_delete',
                'description' => 'حذف البطاقات الشخصية نهائيًا من النظام',
            ],
            [
                'name' => 'identity_cards.approve',
                'name_ar' => 'اعتماد البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'approve',
                'description' => 'اعتماد طلبات البطاقات الشخصية',
            ],
            [
                'name' => 'identity_cards.reject',
                'name_ar' => 'رفض البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'reject',
                'description' => 'رفض طلبات البطاقات الشخصية',
            ],
            [
                'name' => 'identity_cards.resubmit',
                'name_ar' => 'إعادة تقديم البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'resubmit',
                'description' => 'إعادة تقديم طلبات البطاقات الشخصية المرفوضة',
            ],
            [
                'name' => 'identity_cards.print',
                'name_ar' => 'طباعة البطاقات الشخصية',
                'module' => 'identity_cards',
                'action' => 'print',
                'description' => 'طباعة البطاقات الشخصية بصيغة PDF',
            ],

            /*
|--------------------------------------------------------------------------
| Family Cards
|--------------------------------------------------------------------------
*/

            [
                'name' => 'family_cards.view',
                'name_ar' => 'عرض البطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'view',
                'description' => 'عرض بيانات البطاقات العائلية',
            ],
            [
                'name' => 'family_cards.create',
                'name_ar' => 'إصدار بطاقة عائلية',
                'module' => 'family_cards',
                'action' => 'create',
                'description' => 'إنشاء طلب إصدار بطاقة عائلية جديدة',
            ],
            [
                'name' => 'family_cards.update',
                'name_ar' => 'تعديل البطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'update',
                'description' => 'تعديل بيانات البطاقات العائلية',
            ],
            [
                'name' => 'family_cards.delete',
                'name_ar' => 'حذف البطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'delete',
                'description' => 'حذف البطاقات العائلية ونقلها إلى سلة المحذوفات',
            ],
            [
                'name' => 'family_cards.restore',
                'name_ar' => 'استعادة البطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'restore',
                'description' => 'استعادة البطاقات العائلية المحذوفة',
            ],
            [
                'name' => 'family_cards.force_delete',
                'name_ar' => 'الحذف النهائي للبطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'force_delete',
                'description' => 'حذف البطاقات العائلية نهائيًا من النظام',
            ],
            [
                'name' => 'family_cards.approve',
                'name_ar' => 'اعتماد البطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'approve',
                'description' => 'اعتماد طلبات البطاقات العائلية وتحديد تواريخ الإصدار والانتهاء',
            ],
            [
                'name' => 'family_cards.reject',
                'name_ar' => 'رفض البطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'reject',
                'description' => 'رفض طلبات إصدار البطاقات العائلية',
            ],
            [
                'name' => 'family_cards.print',
                'name_ar' => 'طباعة البطاقات العائلية',
                'module' => 'family_cards',
                'action' => 'print',
                'description' => 'طباعة البطاقات العائلية بصيغة PDF وتسجيل عدد مرات الطباعة',
            ],

            /*
|--------------------------------------------------------------------------
| Birth Certificates
|--------------------------------------------------------------------------
*/

            [
                'name' => 'birth_certificates.view',
                'name_ar' => 'عرض شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'view',
                'description' => 'عرض قائمة شهادات الميلاد وتفاصيل الشهادات',
            ],
            [
                'name' => 'birth_certificates.create',
                'name_ar' => 'إنشاء شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'create',
                'description' => 'إنشاء طلب إصدار شهادة ميلاد جديدة',
            ],
            [
                'name' => 'birth_certificates.update',
                'name_ar' => 'تعديل شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'update',
                'description' => 'تعديل بيانات شهادات الميلاد',
            ],
            [
                'name' => 'birth_certificates.delete',
                'name_ar' => 'حذف شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'delete',
                'description' => 'حذف شهادات الميلاد ونقلها إلى سلة المحذوفات',
            ],
            [
                'name' => 'birth_certificates.restore',
                'name_ar' => 'استعادة شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'restore',
                'description' => 'استعادة شهادات الميلاد المحذوفة',
            ],
            [
                'name' => 'birth_certificates.force_delete',
                'name_ar' => 'الحذف النهائي لشهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'force_delete',
                'description' => 'حذف شهادات الميلاد نهائيًا من النظام',
            ],
            [
                'name' => 'birth_certificates.approve',
                'name_ar' => 'اعتماد شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'approve',
                'description' => 'اعتماد طلبات شهادات الميلاد وتحديد تاريخ الإصدار',
            ],
            [
                'name' => 'birth_certificates.print',
                'name_ar' => 'طباعة شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'print',
                'description' => 'طباعة شهادات الميلاد بصيغة PDF وتسجيل عدد مرات الطباعة',
            ],
            [
                'name' => 'birth_certificates.cancel',
                'name_ar' => 'إلغاء شهادات الميلاد',
                'module' => 'birth_certificates',
                'action' => 'cancel',
                'description' => 'إلغاء طلب شهادة الميلاد وتغيير حالته إلى ملغاة',
            ],
            /*
            |--------------------------------------------------------------------------
            | Death Certificates
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'death_certificates.view',
                'name_ar' => 'عرض شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'view',
                'description' => 'عرض قائمة شهادات الوفاة وتفاصيل الشهادات',
            ],
            [
                'name' => 'death_certificates.create',
                'name_ar' => 'إنشاء شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'create',
                'description' => 'إنشاء طلب إصدار شهادة وفاة جديدة',
            ],
            [
                'name' => 'death_certificates.update',
                'name_ar' => 'تعديل شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'update',
                'description' => 'تعديل بيانات شهادات الوفاة',
            ],
            [
                'name' => 'death_certificates.delete',
                'name_ar' => 'حذف شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'delete',
                'description' => 'حذف شهادات الوفاة ونقلها إلى سلة المحذوفات',
            ],
            [
                'name' => 'death_certificates.restore',
                'name_ar' => 'استعادة شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'restore',
                'description' => 'استعادة شهادات الوفاة المحذوفة',
            ],
            [
                'name' => 'death_certificates.force_delete',
                'name_ar' => 'الحذف النهائي لشهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'force_delete',
                'description' => 'حذف شهادات الوفاة نهائيًا من النظام',
            ],
            [
                'name' => 'death_certificates.approve',
                'name_ar' => 'اعتماد شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'approve',
                'description' => 'اعتماد طلبات شهادات الوفاة وتحديد تاريخ الإصدار',
            ],
            [
                'name' => 'death_certificates.print',
                'name_ar' => 'طباعة شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'print',
                'description' => 'طباعة شهادات الوفاة بصيغة PDF وتسجيل عدد مرات الطباعة',
            ],
            [
                'name' => 'death_certificates.cancel',
                'name_ar' => 'إلغاء شهادات الوفاة',
                'module' => 'death_certificates',
                'action' => 'cancel',
                'description' => 'إلغاء طلب شهادة الوفاة وتغيير حالته إلى ملغاة',
            ],
            [
                'name' => 'appointments.view',
                'name_ar' => 'عرض المواعيد',
                'module' => 'appointments',
                'action' => 'view',
                'description' => 'عرض قائمة المواعيد وتفاصيلها',
            ],

            [
                'name' => 'appointments.create',
                'name_ar' => 'حجز موعد',
                'module' => 'appointments',
                'action' => 'create',
                'description' => 'إنشاء موعد جديد وحجز موعد للمواطن',
            ],

            [
                'name' => 'appointments.update',
                'name_ar' => 'تعديل المواعيد',
                'module' => 'appointments',
                'action' => 'update',
                'description' => 'تعديل بيانات الموعد',
            ],

            [
                'name' => 'appointments.delete',
                'name_ar' => 'حذف المواعيد',
                'module' => 'appointments',
                'action' => 'delete',
                'description' => 'حذف المواعيد',
            ],

            [
                'name' => 'appointments.restore',
                'name_ar' => 'استعادة المواعيد',
                'module' => 'appointments',
                'action' => 'restore',
                'description' => 'استعادة المواعيد المحذوفة soft delete',
            ],

            [
                'name' => 'appointments.force_delete',
                'name_ar' => 'الحذف النهائي للمواعيد',
                'module' => 'appointments',
                'action' => 'force_delete',
                'description' => 'حذف المواعيد نهائيًا من قاعدة البيانات',
            ],

            [
                'name' => 'appointments.confirm',
                'name_ar' => 'تأكيد المواعيد',
                'module' => 'appointments',
                'action' => 'confirm',
                'description' => 'تأكيد الموعد وتسجيل المستخدم الذي أكده ووقت التأكيد',
            ],

            [
                'name' => 'appointments.attend',
                'name_ar' => 'تسجيل الحضور',
                'module' => 'appointments',
                'action' => 'attend',
                'description' => 'تسجيل حضور المواطن للموعد',
            ],

            [
                'name' => 'appointments.no_show',
                'name_ar' => 'تسجيل عدم الحضور',
                'module' => 'appointments',
                'action' => 'no_show',
                'description' => 'تسجيل عدم حضور المواطن للموعد',
            ],

            [
                'name' => 'appointments.cancel',
                'name_ar' => 'إلغاء المواعيد',
                'module' => 'appointments',
                'action' => 'cancel',
                'description' => 'إلغاء الموعد إذا كانت حالته قيد الانتظار أو مؤكدًا',
            ],
            [
                'name' => 'branches.view',
                'name_ar' => 'عرض الفروع',
                'module' => 'branches',
                'action' => 'view',
                'description' => 'عرض قائمة الفروع وتفاصيلها',
            ],

            [
                'name' => 'branches.create',
                'name_ar' => 'إضافة فرع',
                'module' => 'branches',
                'action' => 'create',
                'description' => 'إنشاء فرع جديد في النظام',
            ],

            [
                'name' => 'branches.update',
                'name_ar' => 'تعديل الفروع',
                'module' => 'branches',
                'action' => 'update',
                'description' => 'تعديل بيانات الفروع الموجودة',
            ],

            [
                'name' => 'branches.delete',
                'name_ar' => 'حذف الفروع',
                'module' => 'branches',
                'action' => 'delete',
                'description' => 'حذف الفروع غير المرتبطة بمستخدمين',
            ],

            [
                'name' => 'branches.restore',
                'name_ar' => 'استعادة الفروع',
                'module' => 'branches',
                'action' => 'restore',
                'description' => 'استعادة الفروع المحذوفة',
            ],

            [
                'name' => 'branches.force_delete',
                'name_ar' => 'الحذف النهائي للفروع',
                'module' => 'branches',
                'action' => 'force_delete',
                'description' => 'حذف الفروع نهائيًا من قاعدة البيانات',
            ],


            [
                'name' => 'districts.view',
                'name_ar' => 'عرض المديريات',
                'module' => 'districts',
                'action' => 'view',
                'description' => 'عرض قائمة المديريات وتفاصيلها',
            ],

            [
                'name' => 'districts.create',
                'name_ar' => 'إضافة مديرية',
                'module' => 'districts',
                'action' => 'create',
                'description' => 'إنشاء مديرية جديدة وربطها بمحافظة',
            ],

            [
                'name' => 'districts.update',
                'name_ar' => 'تعديل المديريات',
                'module' => 'districts',
                'action' => 'update',
                'description' => 'تعديل بيانات المديريات الموجودة',
            ],

            [
                'name' => 'districts.delete',
                'name_ar' => 'حذف المديريات',
                'module' => 'districts',
                'action' => 'delete',
                'description' => 'حذف المديريات',
            ],

            [
                'name' => 'districts.restore',
                'name_ar' => 'استعادة المديريات',
                'module' => 'districts',
                'action' => 'restore',
                'description' => 'استعادة المديريات المحذوفة',
            ],

            [
                'name' => 'districts.force_delete',
                'name_ar' => 'الحذف النهائي للمديريات',
                'module' => 'districts',
                'action' => 'force_delete',
                'description' => 'حذف المديريات نهائيًا من قاعدة البيانات',
            ],

            [
                'name' => 'governorates.view',
                'name_ar' => 'عرض المحافظات',
                'module' => 'governorates',
                'action' => 'view',
                'description' => 'عرض قائمة المحافظات وتفاصيلها',
            ],

            [
                'name' => 'governorates.create',
                'name_ar' => 'إنشاء محافظة',
                'module' => 'governorates',
                'action' => 'create',
                'description' => 'إضافة محافظة جديدة إلى النظام',
            ],

            [
                'name' => 'governorates.update',
                'name_ar' => 'تعديل المحافظات',
                'module' => 'governorates',
                'action' => 'update',
                'description' => 'تعديل بيانات المحافظات المسجلة',
            ],

            [
                'name' => 'governorates.delete',
                'name_ar' => 'حذف المحافظات',
                'module' => 'governorates',
                'action' => 'delete',
                'description' => 'حذف المحافظات حذفًا عاديًا',
            ],

            [
                'name' => 'governorates.restore',
                'name_ar' => 'استعادة المحافظات',
                'module' => 'governorates',
                'action' => 'restore',
                'description' => 'استعادة المحافظات المحذوفة حذفًا ناعمًا',
            ],

            [
                'name' => 'governorates.force_delete',
                'name_ar' => 'الحذف النهائي للمحافظات',
                'module' => 'governorates',
                'action' => 'force_delete',
                'description' => 'حذف المحافظات نهائيًا من قاعدة البيانات',
            ],
            [
                'name' => 'service_requests.view',
                'name_ar' => 'عرض الطلبات',
                'module' => 'service_requests',
                'action' => 'view',
                'description' => 'عرض قائمة الطلبات وتفاصيلها',
            ],

            [
                'name' => 'service_requests.create',
                'name_ar' => 'إنشاء الطلبات',
                'module' => 'service_requests',
                'action' => 'create',
                'description' => 'إنشاء طلب خدمة جديد',
            ],

            [
                'name' => 'service_requests.update',
                'name_ar' => 'تعديل الطلبات',
                'module' => 'service_requests',
                'action' => 'update',
                'description' => 'تعديل بيانات طلبات الخدمات',
            ],

            [
                'name' => 'service_requests.delete',
                'name_ar' => 'حذف الطلبات',
                'module' => 'service_requests',
                'action' => 'delete',
                'description' => 'حذف طلبات الخدمات حذفًا عاديًا',
            ],

            [
                'name' => 'service_requests.restore',
                'name_ar' => 'استعادة الطلبات',
                'module' => 'service_requests',
                'action' => 'restore',
                'description' => 'استعادة طلبات الخدمات المحذوفة حذفًا ناعمًا',
            ],

            [
                'name' => 'service_requests.force_delete',
                'name_ar' => 'الحذف النهائي للطلبات',
                'module' => 'service_requests',
                'action' => 'force_delete',
                'description' => 'حذف طلبات الخدمات نهائيًا من قاعدة البيانات',
            ],

            [
                'name' => 'service_requests.approve',
                'name_ar' => 'الموافقة على الطلبات',
                'module' => 'service_requests',
                'action' => 'approve',
                'description' => 'اعتماد طلبات الخدمات وإنشاء بطاقة الهوية عند انطباق نوع الخدمة',
            ],

            [
                'name' => 'service_requests.reject',
                'name_ar' => 'رفض الطلبات',
                'module' => 'service_requests',
                'action' => 'reject',
                'description' => 'رفض طلبات الخدمات مع تسجيل سبب الرفض',
            ],


            [
                'name' => 'service_fees.view',
                'name_ar' => 'عرض الرسوم',
                'module' => 'service_fees',
                'action' => 'view',
                'description' => 'عرض قائمة رسوم الخدمات وتفاصيلها',
            ],

            [
                'name' => 'service_fees.create',
                'name_ar' => 'إنشاء الرسوم',
                'module' => 'service_fees',
                'action' => 'create',
                'description' => 'إنشاء رسوم جديدة للخدمات',
            ],

            [
                'name' => 'service_fees.update',
                'name_ar' => 'تعديل الرسوم',
                'module' => 'service_fees',
                'action' => 'update',
                'description' => 'تعديل بيانات رسوم الخدمات',
            ],

            [
                'name' => 'service_fees.delete',
                'name_ar' => 'حذف الرسوم',
                'module' => 'service_fees',
                'action' => 'delete',
                'description' => 'حذف رسوم الخدمات حذفًا عاديًا',
            ],

            [
                'name' => 'service_fees.restore',
                'name_ar' => 'استعادة الرسوم',
                'module' => 'service_fees',
                'action' => 'restore',
                'description' => 'استعادة رسوم الخدمات المحذوفة',
            ],

            [
                'name' => 'service_fees.force_delete',
                'name_ar' => 'الحذف النهائي للرسوم',
                'module' => 'service_fees',
                'action' => 'force_delete',
                'description' => 'حذف رسوم الخدمات نهائيًا من قاعدة البيانات',
            ],

            [
                'name' => 'reports.view',
                'name_ar' => 'عرض التقارير',
                'module' => 'reports',
                'action' => 'view',
                'description' => 'عرض قائمة التقارير وتفاصيلها',
            ],

            [
                'name' => 'reports.create',
                'name_ar' => 'إنشاء التقارير',
                'module' => 'reports',
                'action' => 'create',
                'description' => 'إنشاء التقارير وتوليد ملفاتها',
            ],

            [
                'name' => 'reports.update',
                'name_ar' => 'تعديل التقارير',
                'module' => 'reports',
                'action' => 'update',
                'description' => 'تعديل بيانات التقارير',
            ],

            [
                'name' => 'reports.delete',
                'name_ar' => 'حذف التقارير',
                'module' => 'reports',
                'action' => 'delete',
                'description' => 'حذف التقارير حذفًا عاديًا',
            ],

            [
                'name' => 'reports.restore',
                'name_ar' => 'استعادة التقارير',
                'module' => 'reports',
                'action' => 'restore',
                'description' => 'استعادة التقارير المحذوفة',
            ],

            [
                'name' => 'reports.force_delete',
                'name_ar' => 'الحذف النهائي للتقارير',
                'module' => 'reports',
                'action' => 'force_delete',
                'description' => 'حذف التقارير نهائيًا من قاعدة البيانات',
            ],

            [
                'name' => 'reports.download',
                'name_ar' => 'تحميل التقارير',
                'module' => 'reports',
                'action' => 'download',
                'description' => 'تحميل ملفات التقارير المكتملة',
            ],

            [
                'name' => 'backups.view',
                'name_ar' => 'عرض النسخ الاحتياطية',
                'module' => 'backups',
                'action' => 'view',
                'description' => 'عرض قائمة النسخ الاحتياطية وتفاصيلها',
            ],
            [
                'name' => 'backups.create',
                'name_ar' => 'إنشاء نسخة احتياطية',
                'module' => 'backups',
                'action' => 'create',
                'description' => 'إنشاء نسخة احتياطية يدوية لقاعدة البيانات وملفات النظام',
            ],
            [
                'name' => 'backups.delete',
                'name_ar' => 'حذف النسخ الاحتياطية',
                'module' => 'backups',
                'action' => 'delete',
                'description' => 'حذف النسخ الاحتياطية وملفاتها وسجلاتها',
            ],
            [
                'name' => 'backups.download',
                'name_ar' => 'تنزيل النسخ الاحتياطية',
                'module' => 'backups',
                'action' => 'download',
                'description' => 'تنزيل ملفات النسخ الاحتياطية المكتملة',
            ],


            [
                'name' => 'audit_logs.view',
                'name_ar' => 'عرض سجل التدقيق',
                'module' => 'audit_logs',
                'action' => 'view',
                'description' => 'عرض قائمة سجلات التدقيق وتفاصيل العمليات التي تمت داخل النظام',
            ],


            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'users.view',
                'name_ar' => 'عرض المستخدمين',
                'module' => 'users',
                'action' => 'view',
                'description' => 'عرض المستخدمين',
            ],
            [
                'name' => 'users.create',
                'name_ar' => 'إضافة المستخدمين',
                'module' => 'users',
                'action' => 'create',
                'description' => 'إضافة مستخدم جديد',
            ],
            [
                'name' => 'users.edit',
                'name_ar' => 'تعديل المستخدمين',
                'module' => 'users',
                'action' => 'edit',
                'description' => 'تعديل بيانات المستخدمين',
            ],
            [
                'name' => 'users.delete',
                'name_ar' => 'حذف المستخدمين',
                'module' => 'users',
                'action' => 'delete',
                'description' => 'حذف المستخدمين',
            ],

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'roles.view',
                'name_ar' => 'عرض الأدوار',
                'module' => 'roles',
                'action' => 'view',
                'description' => 'عرض الأدوار والصلاحيات',
            ],
            [
                'name' => 'roles.create',
                'name_ar' => 'إضافة الأدوار',
                'module' => 'roles',
                'action' => 'create',
                'description' => 'إضافة دور جديد',
            ],
            [
                'name' => 'roles.edit',
                'name_ar' => 'تعديل الأدوار',
                'module' => 'roles',
                'action' => 'edit',
                'description' => 'تعديل الأدوار والصلاحيات',
            ],
            [
                'name' => 'roles.delete',
                'name_ar' => 'حذف الأدوار',
                'module' => 'roles',
                'action' => 'delete',
                'description' => 'حذف الأدوار',
            ],
            [
                'name' => 'settings.backup',
                'name_ar' => 'إدارة إعدادات النسخ الاحتياطي',
                'module' => 'settings',
                'action' => 'backup',
                'description' => 'إدارة إعدادات النسخ الاحتياطي التلقائي',
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
                'name' => 'مدير النظام',
            ],
            [
                'name' => 'موظف التسجيل',
            ],
            [
                'name' => 'موظف الجوازات',
            ],
            [
                'name' => 'المعتمد',
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

        $admin = Role::where('name', 'مدير النظام')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $registrationOfficer = Role::where('name', 'موظف التسجيل')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $passportOfficer = Role::where('name', 'موظف الجوازات')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $approver = Role::where('name', 'المعتمد')
            ->where('guard_name', 'web')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */

        // Admin
        // يحصل على جميع الصلاحيات في النظام
        $admin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // Registration Officer
        $registrationOfficer->syncPermissions([
            'citizens.view',
            'citizens.create',
            'citizens.edit',
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
