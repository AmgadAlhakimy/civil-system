<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use App\Models\Passport;
use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ViewPassport extends ViewRecord
{
    protected static string $resource = PassportResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (! auth()->user()?->can("passports.view")) {
            throw new AuthorizationException(
                "ليس لديك صلاحية عرض بيانات الجواز."
            );
        }
    }

    protected function authorizePassportAction(string $permission): void
    {
        if (! auth()->user()?->can($permission)) {
            throw new AuthorizationException(
                "ليس لديك صلاحية لتنفيذ هذه العملية."
            );
        }
    }

    protected function arabicText(mixed $text): string
    {
        if ($text === null || $text === "") {
            return "-";
        }

        $text = (string) $text;

        if (! preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return $text;
        }

        $arabic = new Arabic();

        return $arabic->utf8Glyphs($text);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make("approve")
                ->label("اعتماد الجواز")
                ->icon("heroicon-o-check-circle")
                ->color("success")
                ->requiresConfirmation()
                ->modalHeading("اعتماد الجواز")
                ->modalDescription(
                    "هل أنت متأكد من اعتماد هذا الجواز؟ سيتم تحديد تاريخ الإصدار تلقائيًا، وتكون مدة الصلاحية 6 سنوات، وسيصبح الجواز ساريًا."
                )
                ->modalSubmitActionLabel("نعم، اعتماد")
                ->modalCancelActionLabel("إلغاء")
                ->visible(
                    fn (): bool =>
                        $this->record->status === "pending"
                        && (auth()->user()?->can("passports.approve") ?? false)
                )
                ->action(function (): void {
                    $this->authorizePassportAction("passports.approve");

                    $issueDate = now();

                    $approved = DB::transaction(function () use ($issueDate): bool {
                        $passport = Passport::query()
                            ->whereKey($this->record->getKey())
                            ->lockForUpdate()
                            ->first();

                        if (! $passport || $passport->status !== "pending") {
                            return false;
                        }

                        $passport->update([
                            "status" => "active",
                            "approved_by" => auth()->id(),
                            "approved_at" => $issueDate,
                            "issue_date" => $issueDate->toDateString(),
                            "expiry_date" => $issueDate
                                ->copy()
                                ->addYears($passport->validityYears())
                                ->toDateString(),
                            "rejection_reason" => null,
                            "verified_at" => null,
                        ]);

                        return true;
                    });

                    if (! $approved) {
                        $this->record->refresh();

                        Notification::make()
                            ->title("لا يمكن اعتماد الجواز")
                            ->body("حالة الجواز تغيرت ولم يعد الطلب قيد الانتظار.")
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->refresh();

                    $this->refreshFormData([
                        "status",
                        "approved_by",
                        "approved_at",
                        "issue_date",
                        "expiry_date",
                        "rejection_reason",
                        "verified_at",
                    ]);

                    Notification::make()
                        ->title("تم اعتماد الجواز بنجاح")
                        ->body("تم إصدار الجواز وأصبح ساريًا لمدة 6 سنوات.")
                        ->success()
                        ->send();
                }),

            Action::make("reject")
                ->label("رفض الجواز")
                ->icon("heroicon-o-x-circle")
                ->color("danger")
                ->visible(
                    fn (): bool =>
                        $this->record->status === "pending"
                        && (auth()->user()?->can("passports.reject") ?? false)
                )
                ->schema([
                    Textarea::make("rejection_reason")
                        ->label("سبب الرفض")
                        ->placeholder("اكتب سبب رفض الجواز بالتفصيل")
                        ->required()
                        ->minLength(5)
                        ->maxLength(2000)
                        ->rows(4)
                        ->validationMessages([
                            "required" => "يجب كتابة سبب رفض الجواز.",
                            "min" => "يجب ألا يقل سبب الرفض عن 5 أحرف.",
                            "max" => "يجب ألا يتجاوز سبب الرفض 2000 حرف.",
                        ]),
                ])
                ->requiresConfirmation()
                ->modalHeading("رفض الجواز")
                ->modalDescription(
                    "اكتب سبب الرفض ثم أكد العملية. سيُحفظ السبب ضمن بيانات الجواز."
                )
                ->modalSubmitActionLabel("تأكيد الرفض")
                ->modalCancelActionLabel("إلغاء")
                ->action(function (array $data): void {
                    $this->authorizePassportAction("passports.reject");

                    $rejected = DB::transaction(function () use ($data): bool {
                        $passport = Passport::query()
                            ->whereKey($this->record->getKey())
                            ->lockForUpdate()
                            ->first();

                        if (! $passport || $passport->status !== "pending") {
                            return false;
                        }

                        $passport->update([
                            "status" => "rejected",
                            "rejection_reason" => trim($data["rejection_reason"]),
                            "verified_at" => now(),
                            "approved_by" => null,
                            "approved_at" => null,
                            "issue_date" => null,
                            "expiry_date" => null,
                        ]);

                        return true;
                    });

                    if (! $rejected) {
                        $this->record->refresh();

                        Notification::make()
                            ->title("لا يمكن رفض الجواز")
                            ->body("حالة الجواز تغيرت ولم يعد الطلب قيد الانتظار.")
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->refresh();

                    $this->refreshFormData([
                        "status",
                        "rejection_reason",
                        "verified_at",
                        "approved_by",
                        "approved_at",
                        "issue_date",
                        "expiry_date",
                    ]);

                    Notification::make()
                        ->title("تم رفض الجواز")
                        ->body("تم حفظ سبب الرفض بنجاح.")
                        ->danger()
                        ->send();
                }),

            Action::make("resubmit")
                ->label("إعادة تقديم للمراجعة")
                ->icon("heroicon-o-arrow-path")
                ->color("warning")
                ->requiresConfirmation()
                ->modalHeading("إعادة تقديم الجواز")
                ->modalDescription(
                    "تأكد من تصحيح البيانات المطلوبة قبل إعادة تقديم الجواز للمراجعة."
                )
                ->modalSubmitActionLabel("إعادة التقديم")
                ->modalCancelActionLabel("إلغاء")
                ->visible(
                    fn (): bool =>
                        $this->record->status === "rejected"
                        && (auth()->user()?->can("passports.edit") ?? false)
                )
                ->action(function (): void {
                    $this->authorizePassportAction("passports.edit");

                    $resubmitted = DB::transaction(function (): bool {
                        $passport = Passport::query()
                            ->whereKey($this->record->getKey())
                            ->lockForUpdate()
                            ->first();

                        if (! $passport || $passport->status !== "rejected") {
                            return false;
                        }

                        $passport->update([
                            "status" => "pending",
                            "rejection_reason" => null,
                            "verified_at" => null,
                            "approved_by" => null,
                            "approved_at" => null,
                            "issue_date" => null,
                            "expiry_date" => null,
                        ]);

                        return true;
                    });

                    if (! $resubmitted) {
                        $this->record->refresh();

                        Notification::make()
                            ->title("تعذرت إعادة تقديم الجواز")
                            ->body("حالة الجواز تغيرت، يرجى تحديث الصفحة والمحاولة مجددًا.")
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->refresh();

                    $this->refreshFormData([
                        "status",
                        "rejection_reason",
                        "verified_at",
                        "approved_by",
                        "approved_at",
                        "issue_date",
                        "expiry_date",
                    ]);

                    Notification::make()
                        ->title("تمت إعادة تقديم الجواز")
                        ->body("أصبح طلب الجواز بانتظار المراجعة.")
                        ->success()
                        ->send();
                }),

            Action::make("print")
                ->label("طباعة الجواز")
                ->icon("heroicon-o-printer")
                ->color("primary")
                ->requiresConfirmation()
                ->modalHeading("طباعة الجواز")
                ->modalDescription(
                    "هل أنت متأكد من طباعة هذا الجواز؟ سيتم تسجيل عملية الطباعة في النظام."
                )
                ->modalSubmitActionLabel("نعم، طباعة")
                ->modalCancelActionLabel("إلغاء")
                ->visible(
                    fn (): bool =>
                        $this->record->status === "active"
                        && (auth()->user()?->can("passports.view") ?? false)
                )
                ->action(function () {
                    $this->authorizePassportAction("passports.view");

                    $this->record->refresh();

                    if ($this->record->status !== "active") {
                        Notification::make()
                            ->title("لا يمكن طباعة الجواز")
                            ->body("يجب أن يكون الجواز ساريًا قبل طباعته.")
                            ->warning()
                            ->send();

                        return;
                    }

                    if (! $this->record->issue_date || ! $this->record->expiry_date) {
                        Notification::make()
                            ->title("لا يمكن طباعة الجواز")
                            ->body("تاريخ إصدار أو انتهاء الجواز غير موجود.")
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->record->load("citizen");

                    $citizen = $this->record->citizen;

                    $fullName = collect([
                        $citizen?->first_name,
                        $citizen?->father_name,
                        $citizen?->middle_name,
                        $citizen?->last_name,
                    ])
                        ->filter()
                        ->join(" ");

                    $fullNameEn = collect([
                        $citizen?->first_name_en,
                        $citizen?->father_name_en,
                        $citizen?->middle_name_en,
                        $citizen?->last_name_en,
                    ])
                        ->filter()
                        ->join(" ");

                    $type = match ($this->record->type) {
                        "ordinary" => "عادي",
                        "diplomatic" => "دبلوماسي",
                        "official" => "رسمي",
                        default => "غير محدد",
                    };

                    $status = match ($this->record->status) {
                        "pending" => "قيد الانتظار",
                        "rejected" => "مرفوض",
                        "active" => "ساري",
                        "expired" => "منتهي",
                        "cancelled" => "ملغي",
                        "lost" => "مفقود",
                        "damaged" => "تالف",
                        default => "غير محدد",
                    };

                    $authorityAr = $this->record->issuing_authority_ar
                        ?: "مصلحة الهجرة والجوازات - الجمهورية اليمنية";

                    $authorityEn = $this->record->issuing_authority_en ?: "-";

                    $pdf = Pdf::loadView("passports.pdf", [
                        "passport" => $this->record,
                        "citizen" => $citizen,
                        "fullName" => $this->arabicText($fullName),
                        "fullNameEn" => $fullNameEn ?: "-",
                        "birthPlaceEn" => $citizen?->birth_place_en ?: "-",
                        "occupationEn" => $citizen?->occupation_en ?: "-",
                        "type" => $this->arabicText($type),
                        "status" => $this->arabicText($status),
                        "authority" => $this->arabicText($authorityAr),
                        "authorityEnglish" => $authorityEn,
                        "documentTitle" => $this->arabicText(
                            "مستخرج بيانات جواز سفر"
                        ),
                        "countryName" => $this->arabicText(
                            "الجمهورية اليمنية"
                        ),
                        "photoLabel" => $this->arabicText(
                            "صورة صاحب الجواز"
                        ),
                        "photoUnavailable" => $this->arabicText(
                            "صورة غير متوفرة"
                        ),
                        "printCountLabel" => $this->arabicText(
                            "عدد مرات الطباعة"
                        ),
                        "verificationLabel" => $this->arabicText(
                            "رمز التحقق الإلكتروني"
                        ),
                        "footerText" => $this->arabicText(
                            "هذه الوثيقة مستخرجة إلكترونياً من نظام السجل المدني والجوازات بالجمهورية اليمنية."
                        ),
                        "warningText" => $this->arabicText(
                            "أي كشط أو تعديل في هذه الوثيقة يلغي صحتها."
                        ),
                        "passportNumberLabel" => $this->arabicText(
                            "رقم الجواز"
                        ),
                        "nationalIdLabel" => $this->arabicText(
                            "الرقم الوطني"
                        ),
                        "fullNameLabel" => $this->arabicText(
                            "الاسم الكامل"
                        ),
                        "typeLabel" => $this->arabicText(
                            "نوع الجواز"
                        ),
                        "statusLabel" => $this->arabicText(
                            "حالة الجواز"
                        ),
                        "issueDateLabel" => $this->arabicText(
                            "تاريخ الإصدار"
                        ),
                        "expiryDateLabel" => $this->arabicText(
                            "تاريخ الانتهاء"
                        ),
                        "authorityLabel" => $this->arabicText(
                            "جهة الإصدار"
                        ),
                        "authorityEnglishLabel" => "Issuing Authority",
                        "qrLabel" => $this->arabicText(
                            "رمز التحقق الإلكتروني"
                        ),
                    ]);

                    $this->record->increment("print_count");

                    Notification::make()
                        ->title("تمت طباعة الجواز بنجاح")
                        ->body(
                            "تم تسجيل عملية الطباعة رقم "
                            . $this->record->fresh()->print_count
                        )
                        ->success()
                        ->send();

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "passport-" . $this->record->passport_number . ".pdf"
                    );
                }),

            EditAction::make()
                ->label("تعديل الجواز")
                ->icon("heroicon-o-pencil-square")
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can("passports.edit") ?? false
                ),

            DeleteAction::make()
                ->label("حذف الجواز")
                ->icon("heroicon-o-trash")
                ->color("danger")
                ->requiresConfirmation()
                ->modalHeading("حذف الجواز")
                ->modalDescription(
                    "هل أنت متأكد من حذف هذا الجواز؟ سيتم نقله إلى سلة المهملات."
                )
                ->modalSubmitActionLabel("نعم، حذف الجواز")
                ->modalCancelActionLabel("إلغاء")
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can("passports.delete") ?? false
                ),
        ];
    }
}
