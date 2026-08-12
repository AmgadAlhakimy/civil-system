<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use App\Models\Passport;
use Filament\Resources\Pages\CreateRecord;

class CreatePassport extends CreateRecord
{
    protected static string $resource = PassportResource::class;

    protected function handleRecordCreation(array $data): Passport
    {
        $passport = new Passport();

        $passport->fill($data);

        $passport->issued_by = auth()->id();
        $passport->status = 'pending';
        $passport->print_count = 0;
        $passport->approved_by = null;
        $passport->approved_at = null;

        $passport->save();

        return $passport;
    }
}
