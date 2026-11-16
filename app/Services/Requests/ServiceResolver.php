<?php

namespace App\Services\Requests;

class ServiceResolver
{
    private static array $handlers = [
        'issue_id_card'          => IdentityCardIssueHandler::class,
        'renew_id_card'          => IdentityCardRenewalHandler::class,
        'issue_passport'         => PassportHandler::class,
        'passport'               => PassportHandler::class,
        'renew_passport'         => PassportHandler::class,
        'birth_certificate'      => BirthCertificateHandler::class,
        'issue_birth_certificate' => BirthCertificateHandler::class,
        'death_certificate'      => DeathCertificateHandler::class,
        'issue_death_certificate' => DeathCertificateHandler::class,
        'family_card'            => FamilyCardHandler::class,
        'issue_family_card'      => FamilyCardHandler::class,
        'renew_family_card'      => FamilyCardHandler::class,
    ];

    public static function for(string $serviceKey): ?ServiceRequestHandler
    {
        $handlerClass = self::$handlers[$serviceKey] ?? null;
        if (!$handlerClass) return null;
        return app($handlerClass);
    }

    public static function hasHandler(string $serviceKey): bool
    {
        return isset(self::$handlers[$serviceKey]);
    }
}