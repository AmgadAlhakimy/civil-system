<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Contracts\View\View;

class PassportVerificationController extends Controller
{
    public function show(string $passport): View
    {
        $record = Passport::query()
            ->whereKey($passport)
            ->first();

        if (! $record) {
            abort(404);
        }

        return view('passports.verify', [
            'passport' => $record,
            'isValid' => $record->isActive(),
        ]);
    }
}
