<?php

namespace App\Services\Requests;

class ServiceResolver
{
    /**
     * ربط جميع مفاتيح الخدمات بمعالجاتها.
     *
     * يمكن أن يكون للخدمة أكثر من اسم بسبب اختلاف
     * أسماء المفاتيح بين Flutter وLaravel والإصدارات السابقة.
     */
    private static array $handlers = [

        // ==========================================
        // البطاقة الشخصية
        // ==========================================

        'new_id_card' => IdentityCardIssueHandler::class,
        'issue_id_card' => IdentityCardIssueHandler::class,

        'renew_id_card' => IdentityCardRenewalHandler::class,

        // ==========================================
        // جواز السفر
        // ==========================================

        'new_passport' => PassportHandler::class,
        'issue_passport' => PassportHandler::class,
        'passport' => PassportHandler::class,

        'renew_passport' => PassportHandler::class,
        'renewal_passport' => PassportHandler::class,

        // ==========================================
        // شهادة الميلاد
        // ==========================================

        'birth_certificate' => BirthCertificateHandler::class,
        'issue_birth_certificate' => BirthCertificateHandler::class,

        // ==========================================
        // شهادة الوفاة
        // ==========================================

        'death_certificate' => DeathCertificateHandler::class,
        'issue_death_certificate' => DeathCertificateHandler::class,

        // ==========================================
        // البطاقة العائلية
        // ==========================================

        'family_card' => FamilyCardHandler::class,
        'issue_family_card' => FamilyCardHandler::class,

        'renew_family_card' => FamilyCardHandler::class,
        'family_card_renewal' => FamilyCardHandler::class,
    ];

    /**
     * الحصول على المعالج المناسب للخدمة.
     */
    public static function for(string $serviceKey): ?ServiceRequestHandler
    {
        $serviceKey = strtolower(trim($serviceKey));

        $handlerClass = self::$handlers[$serviceKey] ?? null;

        if ($handlerClass === null) {
            return null;
        }

        return app($handlerClass);
    }

    /**
     * التحقق من وجود معالج للخدمة.
     */
    public static function hasHandler(string $serviceKey): bool
    {
        $serviceKey = strtolower(trim($serviceKey));

        return isset(self::$handlers[$serviceKey]);
    }

    /**
     * إرجاع جميع مفاتيح الخدمات المدعومة.
     */
    public static function supportedKeys(): array
    {
        return array_keys(self::$handlers);
    }
}
