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

        .info-table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
            margin-bottom: 15px;
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

        .label-en {
            direction: ltr;
            display: inline-block;
            font-size: 8px;
            font-weight: normal;
        }

        .document-number {
            direction: ltr;
            display: inline-block;
        }

        .national-id {
            direction: ltr;
            display: inline-block;
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

        .section-title {
            font-size: 12px;
            font-weight: bold;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 7px 9px;
            margin-top: 5px;
            margin-bottom: 0;
            text-align: right;
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

        .page-break {
            page-break-inside: avoid;
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

        if (! preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return $text;
        }

        return $arabic->utf8Glyphs($text);
    };

    $certificate->loadMissing([
        'deceased',
    ]);

    $deceased = $certificate->deceased;

    $deceasedName = collect([
        $deceased?->first_name,
        $deceased?->father_name,
        $deceased?->middle_name,
        $deceased?->last_name,
    ])
        ->filter()
        ->join(' ');

    $deceasedName = $shapeArabic($deceasedName);

    $status = match ($certificate->status) {
        'pending' => $shapeArabic('قيد الانتظار'),
        'active' => $shapeArabic('سارية'),
        'cancelled' => $shapeArabic('ملغاة'),
        default => $shapeArabic('غير محددة'),
    };

    $authority = $shapeArabic(
        'مصلحة الأحوال المدنية والسجل المدني - اليمن'
    );

    $documentTitle = $shapeArabic(
        'الجمهورية اليمنية'
    );

    $documentSubtitle = $shapeArabic(
        'شهادة وفاة'
    );

    $certificateNumberLabel = $shapeArabic(
        'رقم شهادة الوفاة'
    );

    $deceasedNameLabel = $shapeArabic(
        'اسم المتوفى'
    );

    $deceasedNationalIdLabel = $shapeArabic(
        'الرقم الوطني للمتوفى'
    );

    $birthDateLabel = $shapeArabic(
        'تاريخ الميلاد'
    );

    $birthPlaceLabel = $shapeArabic(
        'مكان الميلاد'
    );

    $deathDateLabel = $shapeArabic(
        'تاريخ الوفاة'
    );

    $deathPlaceLabel = $shapeArabic(
        'مكان الوفاة'
    );

    $causeOfDeathLabel = $shapeArabic(
        'سبب الوفاة'
    );

    $statusLabel = $shapeArabic(
        'حالة الشهادة'
    );

    $issueDateLabel = $shapeArabic(
        'تاريخ الإصدار'
    );

    $authorityLabel = $shapeArabic(
        'جهة الإصدار'
    );

    $deathDataTitle = $shapeArabic(
        'بيانات الوفاة'
    );

    $qrTitle = $shapeArabic(
        'رمز التحقق الإلكتروني'
    );

    $footerText1 = $shapeArabic(
        'هذه الوثيقة مستخرجة إلكترونياً من نظام السجل المدني بالجمهورية اليمنية.'
    );

    $footerText2 = $shapeArabic(
        'أي كشط أو تعديل في هذه الوثيقة يلغي صحتها.'
    );
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

    <table class="info-table">

        <tr>

            <th>
                {{ $certificateNumberLabel }}
                <br>
                <span class="label-en">
                    Death Certificate No.
                </span>
            </th>

            <td>
                <span class="document-number">
                    {{ $certificate->certificate_number }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $deceasedNameLabel }}
                <br>
                <span class="label-en">
                    Deceased Name
                </span>
            </th>

            <td>
                <span class="arabic-text">
                    {{ $deceasedName ?: '---' }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $deceasedNationalIdLabel }}
                <br>
                <span class="label-en">
                    National ID
                </span>
            </th>

            <td>
                <span class="national-id">
                    {{ $deceased?->national_id ?? '---' }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $birthDateLabel }}
                <br>
                <span class="label-en">
                    Date of Birth
                </span>
            </th>

            <td>
                <span class="ltr-text">
                    {{ $deceased?->birth_date?->format('Y-m-d') ?? '---' }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $birthPlaceLabel }}
                <br>
                <span class="label-en">
                    Place of Birth
                </span>
            </th>

            <td>
                <span class="arabic-text">
                    {{ $shapeArabic($deceased?->birth_place) }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $statusLabel }}
                <br>
                <span class="label-en">
                    Status
                </span>
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
                <span class="label-en">
                    Date of Issue
                </span>
            </th>

            <td>
                <span class="ltr-text">
                    {{ $certificate->issue_date?->format('Y-m-d') ?? '---' }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $authorityLabel }}
                <br>
                <span class="label-en">
                    Authority
                </span>
            </th>

            <td>
                <span class="arabic-text">
                    {{ $authority }}
                </span>
            </td>

        </tr>

    </table>

    <div class="section-title">
        {{ $deathDataTitle }}
    </div>

    <table class="info-table">

        <tr>

            <th>
                {{ $deathDateLabel }}
                <br>
                <span class="label-en">
                    Date of Death
                </span>
            </th>

            <td>
                <span class="ltr-text">
                    {{ $certificate->death_date?->format('Y-m-d') ?? '---' }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $deathPlaceLabel }}
                <br>
                <span class="label-en">
                    Place of Death
                </span>
            </th>

            <td>
                <span class="arabic-text">
                    {{ $shapeArabic($certificate->place_of_death) }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $causeOfDeathLabel }}
                <br>
                <span class="label-en">
                    Cause of Death
                </span>
            </th>

            <td>
                <span class="arabic-text">
                    {{ $shapeArabic($certificate->cause_of_death) }}
                </span>
            </td>

        </tr>

    </table>

    <div class="footer-section">

        <div class="qr-title">
            {{ $qrTitle }}

            <span class="label-en">
                (QR / Code)
            </span>
        </div>

        <div class="qr-box">
            {{ $certificate->qr_code ?: $certificate->certificate_number }}
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
