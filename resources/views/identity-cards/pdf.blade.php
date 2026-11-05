<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 194mm;
            height: 281mm;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #222;
            text-align: right;
        }

        .page-layout {
            width: 100%;
            height: 281mm;
            border-collapse: collapse;
            table-layout: fixed;
            direction: rtl;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .page-half {
            height: 140.5mm;
            vertical-align: middle;
            text-align: center;
            direction: rtl;
            page-break-inside: avoid;
        }

        .card {
            width: 135mm;
            height: 85mm;
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid #b0b0b0;
            border-radius: 3mm;
            background: #f4f6f8;
            position: relative;
            direction: rtl;
            page-break-inside: avoid;
        }

        /* ==================== الوجه الأمامي ==================== */
        .front {
            padding: 3mm 4mm 2mm;
        }

        .front-header {
            width: 100%;
            height: 24mm;
            border-collapse: collapse;
            direction: rtl;
        }

        .front-heading-cell {
            width: 45%;
            text-align: right;
            vertical-align: top;
        }

        .front-logo-cell {
            width: 25%;
            text-align: center;
            vertical-align: top;
        }

        .front-photo-cell {
            width: 30%;
            text-align: left;
            vertical-align: top;
        }

        .government-name {
            font-size: 9px;
            font-weight: bold;
            line-height: 1.6;
            color: #222;
        }

        .card-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #20406e;
            margin-top: 2mm;
            margin-right: 15mm;
        }

        .front-logo {
            width: 18mm;
            height: 18mm;
            object-fit: contain;
        }

        .photo-box {
            width: 18mm;
            height: 22mm;
            object-fit: cover;
            border: 1px solid #999;
            border-radius: 1mm;
        }

        .photo-placeholder {
            width: 18mm;
            height: 22mm;
            border: 1px dashed #999;
            text-align: center;
            line-height: 22mm;
            color: #777;
            font-size: 6px;
            background: #eaeaea;
        }

        .front-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        .front-info td {
            padding: 1mm 0;
            vertical-align: middle;
        }

        .national-id-row {
            text-align: center;
            margin-bottom: 2mm;
        }

        .national-label {
            font-size: 9px;
            font-weight: bold;
            color: #333;
            margin-left: 5px;
        }

        .national-number {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #20406e;
            direction: ltr;
            display: inline-block;
        }

        .full-name {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #111;
        }

        /* تنسيق سطر الميلاد */
        .birth-row {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .date-ltr {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
        }

        .front-footer {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
            position: absolute;
            bottom: 2mm;
            left: 0;
            padding: 0 4mm;
        }

        .front-footer td {
            vertical-align: bottom;
        }

        .blood-type-cell {
            width: 25%;
            text-align: right;
            font-size: 9px;
            font-weight: bold;
        }

        .blood-value {
            color: #b23b3b;
            font-size: 12px;
            margin-right: 5px;
        }

        .front-barcode-cell {
            width: 50%;
            text-align: center;
            direction: ltr;
        }

        .front-barcode-cell img {
            width: 60mm;
            height: 8mm;
        }

        .serial-cell {
            width: 25%;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            direction: ltr;
        }

        /* ==================== الوجه الخلفي ==================== */
        .back {
            padding: 4mm 4mm 2mm;
        }

        .back-top {
            width: 100%;
            height: 25mm;
            border-collapse: collapse;
            direction: rtl;
        }

        .back-info-cell {
            width: 65%;
            vertical-align: top;
        }

        .back-logo-cell {
            width: 35%;
            text-align: left;
            vertical-align: top;
        }

        .back-logo {
            width: 25mm;
            height: 22mm;
            object-fit: contain;
            opacity: 0.7;
            filter: sepia(1) hue-rotate(340deg) saturate(0.5) brightness(0.6);
        }

        .dates-table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
        }

        .dates-table td {
            padding: 1mm 0;
            vertical-align: middle;
        }

        .back-label {
            width: 35%;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
            color: #222;
        }

        /* تنسيق قيم التواريخ في الوجه الخلفي */
        .back-value {
            width: 65%;
            text-align: left; /* محاذاة لليسار لتكون بجانب اللابل */
            font-size: 11px;
            font-weight: bold;
            color: #222;
            direction: ltr; /* إجبار الاتجاه من اليسار لليمين */
            unicode-bidi: embed;
            padding-left: 5mm; /* مسافة بسيطة لليسار */
        }

        .instructions {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
            text-align: center;
        }

        .instructions td {
            font-size: 10px;
            font-weight: bold;
            line-height: 1.8;
            color: #222;
            text-align: center;
        }

        .barcode-area {
            width: 100%;
            height: 35mm;
            text-align: center;
            vertical-align: middle;
            margin-top: 1mm;
        }

        .pdf417-image {
            width: 115mm;
            height: 28mm;
            object-fit: fill;
        }

        .barcode-placeholder {
            width: 115mm;
            height: 25mm;
            margin: auto;
            border: 1px dashed #aaa;
            text-align: center;
            line-height: 25mm;
            color: #777;
            font-size: 8px;
            direction: rtl;
        }

        .back-footer {
            width: 100%;
            border-collapse: collapse;
            position: absolute;
            bottom: 3mm;
            left: 0;
            padding: 0 4mm;
        }

        .back-footer td {
            width: 50%;
            vertical-align: bottom;
        }

        .control-number {
            text-align: left;
            font-size: 14px;
            font-weight: bold;
            direction: ltr;
            color: #333;
        }

        .reference-number {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            direction: ltr;
            color: #333;
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

        // التأكد من وجود حروف عربية قبل معالجتها
        if (! preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return $text;
        }

        return $arabic->utf8Glyphs($text);
    };

    $toDataUri = function (?string $path): ?string {
        if (! $path || ! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path);

        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    };

    $citizen = $identityCard->citizen;

    $fullName = collect([
        $citizen?->first_name,
        $citizen?->father_name,
        $citizen?->middle_name,
        $citizen?->last_name,
    ])
        ->filter(fn ($value) => filled($value))
        ->join(' ');

    $fullName = $shapeArabic($fullName);

    $nationalId = $identityCard->national_id;

    // --- معالجة تاريخ ومكان الميلاد ---
    $birthPlace = $shapeArabic($citizen?->place_of_birth);
    $birthDate = $citizen?->date_of_birth?->format('Y/m/d');
    // -----------------------------------

    $bloodType = $citizen?->blood_type;

    $photoPath = $citizen?->photo
        ? storage_path('app/private/' . $citizen->photo)
        : null;

    $photoData = $toDataUri($photoPath);

    $serialNumber = $identityCard->serial_number;

    $issuePlace = $shapeArabic($identityCard->issue_place);
    $issueDate = $identityCard->issue_date?->format('Y/m/d');
    $expiryDate = $identityCard->expiry_date?->format('Y/m/d');

    $barcodeReference = $identityCard->barcode_reference;

    $controlNumber = $identityCard->control_number;

    $logoPath = public_path('images/eagle-logo.png');

    $logoData = $toDataUri($logoPath);

    $frontBarcodeData = filled($frontBarcode ?? null)
        ? 'data:image/png;base64,' . $frontBarcode
        : null;

    $pdf417Data = filled($pdf417Barcode ?? null)
        ? 'data:image/png;base64,' . $pdf417Barcode
        : null;

    $repText = $shapeArabic('الجمهورية اليمنية');
    $minText = $shapeArabic('وزارة الداخلية');
    $authText = $shapeArabic('مصلحة الأحوال المدنية والسجل المدني');
    $cardTitle = $shapeArabic('بطاقة شخصية');
    $nationalIdLabel = $shapeArabic('الرقم الوطني');
    $birthLabel = $shapeArabic('مكان وتاريخ الميلاد');
    $bloodLabel = $shapeArabic('فصيلة الدم');
    $photoPlaceholder = $shapeArabic('صورة');

    $issuePlaceLabel = $shapeArabic('جهة الإصدار');
    $issueDateLabel = $shapeArabic('تاريخ الإصدار');
    $expiryDateLabel = $shapeArabic('تاريخ الانتهاء');

    $instruction1 = $shapeArabic('البطاقة وثيقة هامة يجب الحفاظ عليها');
    $instruction2 = $shapeArabic('في حالة فقدان البطاقة او تلفها يجب إبلاغ أقرب مركز شرطة');

    $barcodePlaceholder = $shapeArabic('لا يتوفر رمز الباركود');
@endphp

<table class="page-layout">

    <!-- ==================== البطاقة (الوجه الأمامي) ==================== -->
    <tr>
        <td class="page-half">
            <div class="card front">

                <table class="front-header">
                    <tr>
                        <td class="front-heading-cell">
                            <div class="government-name">
                                {{ $repText }}<br>
                                {{ $minText }}<br>
                                {{ $authText }}
                            </div>
                        </td>

                        <td class="front-logo-cell">
                            @if ($logoData)
                                <img src="{{ $logoData }}" class="front-logo" alt="Logo">
                            @endif
                        </td>

                        <td class="front-photo-cell">
                            @if ($photoData)
                                <img src="{{ $photoData }}" class="photo-box" alt="Photo">
                            @else
                                <div class="photo-placeholder">
                                    {{ $photoPlaceholder }}
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="card-title">
                    {{ $cardTitle }}
                </div>

                <table class="front-info">
                    <tr>
                        <td class="national-id-row">
                            <span class="national-number">{{ $nationalId ?: '-' }}</span>
                            <span class="national-label">{{ $nationalIdLabel }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="full-name">
                            {{ $fullName }}
                        </td>
                    </tr>
                    <tr>
                        <td class="birth-row">
                            <!-- عرض مكان الميلاد (عربي) ثم التاريخ (انجليزي) -->
                            {{ $birthPlace }}
                            @if($birthPlace && $birthDate) - @endif
                            <span class="date-ltr" dir="ltr">{{ $birthDate }}</span>
                        </td>
                    </tr>
                </table>

                <table class="front-footer">
                    <tr>
                        <td class="blood-type-cell">
                            {{ $bloodLabel }} <span class="blood-value">{{ $bloodType ?: '-' }}</span>
                        </td>

                        <td class="front-barcode-cell">
                            @if ($frontBarcodeData)
                                <img src="{{ $frontBarcodeData }}" alt="Barcode">
                            @else
                                {{ $nationalId ?: '-' }}
                            @endif
                        </td>

                        <td class="serial-cell">
                            {{ $serialNumber ?: '-' }}
                        </td>
                    </tr>
                </table>

            </div>
        </td>
    </tr>

    <!-- ==================== البطاقة (الوجه الخلفي) ==================== -->
    <tr>
        <td class="page-half">
            <div class="card back">

                <table class="back-top">
                    <tr>
                        <td class="back-info-cell">
                            <table class="dates-table">
                                <tr>
                                    <td class="back-label">{{ $issuePlaceLabel }}</td>
                                    <td class="back-value">{{ $issuePlace }}</td>
                                </tr>
                                <tr>
                                    <td class="back-label">{{ $issueDateLabel }}</td>
                                    <!-- إضافة dir="ltr" للتاريخ -->
                                    <td class="back-value" dir="ltr">{{ $issueDate ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="back-label">{{ $expiryDateLabel }}</td>
                                    <!-- إضافة dir="ltr" للتاريخ -->
                                    <td class="back-value" dir="ltr">{{ $expiryDate ?: '-' }}</td>
                                </tr>
                            </table>
                        </td>

                        <td class="back-logo-cell">
                            @if ($logoData)
                                <img src="{{ $logoData }}" class="back-logo" alt="Logo">
                            @endif
                        </td>
                    </tr>
                </table>

                <table class="instructions">
                    <tr>
                        <td>- {{ $instruction1 }}</td>
                    </tr>
                    <tr>
                        <td>- {{ $instruction2 }}</td>
                    </tr>
                </table>

                <div class="barcode-area">
                    @if ($pdf417Data)
                        <img src="{{ $pdf417Data }}" class="pdf417-image" alt="PDF417 Barcode">
                    @else
                        <div class="barcode-placeholder">{{ $barcodePlaceholder }}</div>
                    @endif
                </div>

                <table class="back-footer">
                    <tr>
                        <td class="reference-number">
                            {{ $barcodeReference ?: '-' }}
                        </td>
                        <td class="control-number">
                            {{ $controlNumber ?: '-' }}
                        </td>
                    </tr>
                </table>

            </div>
        </td>
    </tr>

</table>

</body>
</html>
