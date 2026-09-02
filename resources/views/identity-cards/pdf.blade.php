<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 portrait;
            margin: 30px 25px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 11px;
            color: #222222;
            margin: 0;
            padding: 0;
        }

        .document-wrapper {
            width: 100%;
            border: 1.5px solid #2c3e50;
            padding: 18px;
            background-color: #ffffff;
        }

        .header {
            text-align: center;
            border-bottom: 1.5px solid #e0e0e0;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0 0 5px;
            font-weight: bold;
            text-align: center;
        }

        .header h2 {
            font-size: 13px;
            margin: 0;
            text-align: center;
        }

        .layout-table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
            margin-bottom: 15px;
        }

        .data-column {
            width: 76%;
            vertical-align: top;
            padding: 0 0 0 12px;
        }

        .photo-column {
            width: 24%;
            vertical-align: top;
            text-align: center;
            padding: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            vertical-align: middle;
        }

        .info-table th {
            width: 34%;
            background-color: #f3f4f6;
            color: #374151;
            font-size: 10px;
            font-weight: bold;
            text-align: right;
        }

        .info-table td {
            width: 66%;
            font-size: 11px;
            font-weight: bold;
            color: #111827;
            text-align: right;
            direction: rtl;
        }

        .arabic-text {
            direction: rtl;
            text-align: right;
        }

        .ltr-text {
            direction: ltr;
            text-align: left;
            display: inline-block;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        .english-text {
            direction: ltr;
            text-align: left;
            display: inline-block;
            font-family: DejaVu Sans, sans-serif;
        }

        .label-en {
            direction: ltr;
            display: inline-block;
            font-size: 8px;
            font-weight: normal;
        }

        .photo-container {
            text-align: center;
            direction: rtl;
        }

        .photo-box {
            width: 105px;
            height: 135px;
            border: 1.5px solid #9ca3af;
            border-radius: 5px;
        }

        .photo-placeholder {
            width: 105px;
            height: 135px;
            border: 1.5px dashed #9ca3af;
            border-radius: 5px;
            display: inline-block;
            line-height: 135px;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
            background-color: #f9fafb;
        }

        .photo-label {
            margin-top: 7px;
            font-size: 9px;
            color: #4b5563;
            font-weight: bold;
            text-align: center;
        }

        .print-count {
            margin-top: 12px;
            font-size: 8px;
            color: #555555;
            text-align: center;
        }

        .badge {
            display: inline-block;
            background-color: #dcfce7;
            color: #166534;
            padding: 3px 8px;
            border: 1px solid #bbf7d0;
            border-radius: 3px;
            font-size: 9px;
            direction: rtl;
        }

        .footer-section {
            text-align: center;
            border-top: 1.5px solid #e0e0e0;
            padding-top: 12px;
            margin-top: 8px;
            direction: rtl;
        }

        .qr-title {
            margin-bottom: 6px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        .qr-box {
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            padding: 7px 14px;
            border-radius: 4px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            direction: ltr;
        }

        .footer-text {
            margin-top: 9px;
            font-size: 8px;
            color: #6b7280;
            line-height: 1.7;
            text-align: center;
            direction: rtl;
        }

        .document-number {
            direction: ltr;
            display: inline-block;
        }

        .national-id {
            direction: ltr;
            display: inline-block;
        }
    </style>
</head>

<body>

@php
    use ArPHP\I18N\Arabic;

    $arabic = new Arabic();

    $shapeArabic = function (mixed $text) use ($arabic): string {
        if ($text === null || $text === '') {
            return '-';
        }

        $text = (string) $text;

        if (!preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return $text;
        }

        return $arabic->utf8Glyphs($text);
    };

    $citizen = $identityCard->citizen;

    $fullName = collect([
        $citizen?->first_name,
        $citizen?->father_name,
        $citizen?->middle_name,
        $citizen?->last_name,
    ])
        ->filter()
        ->join(' ');

    $fullName = $shapeArabic($fullName);

    $status = match ($identityCard->status) {
        'pending' => $shapeArabic('قيد الانتظار'),
        'approved' => $shapeArabic('معتمد'),
        'rejected' => $shapeArabic('مرفوض'),
        'active' => $shapeArabic('سارية'),
        'expired' => $shapeArabic('منتهية'),
        'cancelled' => $shapeArabic('ملغاة'),
        'lost' => $shapeArabic('مفقودة'),
        'damaged' => $shapeArabic('تالفة'),
        default => $shapeArabic('غير محدد'),
    };

    $authority = $shapeArabic('مصلحة الأحوال المدنية والسجل المدني - اليمن');

    $photoPlaceholder = $shapeArabic('صورة غير متوفرة');
    $photoLabel = $shapeArabic('صورة صاحب البطاقة');
    $qrTitle = $shapeArabic('رمز التحقق الإلكتروني');

    $footerText1 = $shapeArabic(
        'هذه الوثيقة مستخرجة إلكترونياً من نظام السجل المدني بالجمهورية اليمنية.'
    );

    $footerText2 = $shapeArabic(
        'أي كشط أو تعديل في هذه الوثيقة يلغي صحتها.'
    );

    $idNumberLabel = $shapeArabic('رقم البطاقة');
    $nationalIdLabel = $shapeArabic('الرقم الوطني');
    $fullNameLabel = $shapeArabic('الاسم الكامل');
    $statusLabel = $shapeArabic('حالة البطاقة');
    $issueDateLabel = $shapeArabic('تاريخ الإصدار');
    $expiryDateLabel = $shapeArabic('تاريخ الانتهاء');
    $authorityLabel = $shapeArabic('جهة الإصدار');

    $documentTitle = $shapeArabic('الجمهورية اليمنية');
    $documentSubtitle = $shapeArabic('مستخرج بيانات بطاقة شخصية');

    $photo = $citizen?->photo;
@endphp

<div class="document-wrapper">

    <div class="header">

        <h1>
            {{ $documentTitle }}
        </h1>

        <h2>
            {{ $documentSubtitle }}
        </h2>

    </div>

    <table class="layout-table">

        <tr>

            <td class="data-column">

                <table class="info-table">

                    <tr>
                        <th>
                            {{ $idNumberLabel }}
                            <br>
                            <span class="label-en">Identity Card No.</span>
                        </th>

                        <td>
                            <span class="document-number">
                                {{ $identityCard->id_number }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ $nationalIdLabel }}
                            <br>
                            <span class="label-en">National ID</span>
                        </th>

                        <td>
                            <span class="national-id">
                                {{ $citizen?->national_id ?? '---' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ $fullNameLabel }}
                            <br>
                            <span class="label-en">Full Name</span>
                        </th>

                        <td>
                            <span class="arabic-text">
                                {{ $fullName ?: '---' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ $statusLabel }}
                            <br>
                            <span class="label-en">Status</span>
                        </th>

                        <td>
                            <span class="badge">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ $issueDateLabel }}
                            <br>
                            <span class="label-en">Date of Issue</span>
                        </th>

                        <td>
                            <span class="ltr-text">
                                {{ $identityCard->issue_date?->format('Y-m-d') ?? '---' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ $expiryDateLabel }}
                            <br>
                            <span class="label-en">Date of Expiry</span>
                        </th>

                        <td>
                            <span class="ltr-text">
                                {{ $identityCard->expiry_date?->format('Y-m-d') ?? '---' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ $authorityLabel }}
                            <br>
                            <span class="label-en">Authority</span>
                        </th>

                        <td>
                            <span class="arabic-text">
                                {{ $authority }}
                            </span>
                        </td>
                    </tr>

                </table>

            </td>

            <td class="photo-column">

                <div class="photo-container">

                    @if ($photo && file_exists(storage_path('app/private/' . $photo)))

                        <img
                            src="{{ storage_path('app/private/' . $photo) }}"
                            class="photo-box"
                        >

                    @else

                        <div class="photo-placeholder">
                            {{ $photoPlaceholder }}
                        </div>

                    @endif

                    <div class="photo-label">
                        {{ $photoLabel }}
                    </div>

                </div>

            </td>

        </tr>

    </table>

    <div class="footer-section">

        <div class="qr-title">
            {{ $qrTitle }}
            <span class="english-text">(QR / Code)</span>
        </div>

        <div class="qr-box">
            {{ $identityCard->qr_code ?: $identityCard->id_number }}
        </div>

        <div class="footer-text">

            {{ $footerText1 }}

            <br>

            {{ $footerText2 }}

        </div>

    </div>

</div>

</body>
</html>
