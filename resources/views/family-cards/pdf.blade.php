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

        .members-table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
            margin-bottom: 15px;
        }

        .members-table th,
        .members-table td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            vertical-align: middle;
        }

        .members-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        .members-table td {
            font-size: 10px;
            color: #111827;
            text-align: right;
        }

        .members-table .number-column {
            width: 8%;
            text-align: center;
        }

        .members-table .name-column {
            width: 47%;
        }

        .members-table .relationship-column {
            width: 25%;
            text-align: center;
        }

        .members-table .status-column {
            width: 20%;
            text-align: center;
        }

        .member-status {
            display: inline-block;
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 3px;
            padding: 2px 7px;
            font-size: 8px;
        }

        .no-members {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
            margin-bottom: 15px;
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

    $familyCard->loadMissing([
        'head',
        'members.citizen',
    ]);

    $head = $familyCard->head;

    $headName = collect([
        $head?->first_name,
        $head?->father_name,
        $head?->middle_name,
        $head?->last_name,
    ])
        ->filter()
        ->join(' ');

    $headName = $shapeArabic($headName);

    $status = match ($familyCard->status) {
        'pending' => $shapeArabic('قيد الانتظار'),
        'rejected' => $shapeArabic('مرفوضة'),
        'active' => $shapeArabic('سارية'),
        'expired' => $shapeArabic('منتهية'),
        'cancelled' => $shapeArabic('ملغاة'),
        'lost' => $shapeArabic('مفقودة'),
        'damaged' => $shapeArabic('تالفة'),
        default => $shapeArabic('غير محددة'),
    };

    $authority = $shapeArabic(
        'مصلحة الأحوال المدنية والسجل المدني - اليمن'
    );

    $documentTitle = $shapeArabic('الجمهورية اليمنية');

    $documentSubtitle = $shapeArabic(
        'مستخرج بيانات بطاقة عائلية'
    );

    $cardNumberLabel = $shapeArabic(
        'رقم البطاقة العائلية'
    );

    $nationalIdLabel = $shapeArabic(
        'الرقم الوطني لرب الأسرة'
    );

    $headNameLabel = $shapeArabic(
        'اسم رب الأسرة'
    );

    $statusLabel = $shapeArabic(
        'حالة البطاقة'
    );

    $issueDateLabel = $shapeArabic(
        'تاريخ الإصدار'
    );

    $expiryDateLabel = $shapeArabic(
        'تاريخ الانتهاء'
    );

    $authorityLabel = $shapeArabic(
        'جهة الإصدار'
    );

    $membersTitle = $shapeArabic(
        'أفراد الأسرة'
    );

    $memberNumberLabel = $shapeArabic(
        'م'
    );

    $memberNameLabel = $shapeArabic(
        'اسم الفرد'
    );

    $relationshipLabel = $shapeArabic(
        'صلة القرابة'
    );

    $memberStatusLabel = $shapeArabic(
        'الحالة'
    );

    $noMembersText = $shapeArabic(
        'لا يوجد أفراد مسجلون في هذه البطاقة.'
    );

    $activeText = $shapeArabic(
        'فعال'
    );

    $inactiveText = $shapeArabic(
        'غير فعال'
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
                {{ $cardNumberLabel }}
                <br>
                <span class="label-en">
                    Family Card No.
                </span>
            </th>

            <td>
                <span class="document-number">
                    {{ $familyCard->card_number }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $headNameLabel }}
                <br>
                <span class="label-en">
                    Head of Family
                </span>
            </th>

            <td>
                <span class="arabic-text">
                    {{ $headName ?: '---' }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $nationalIdLabel }}
                <br>
                <span class="label-en">
                    National ID
                </span>
            </th>

            <td>
                <span class="national-id">
                    {{ $head?->national_id ?? '---' }}
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
                    {{ $familyCard->issue_date?->format('Y-m-d') ?? '---' }}
                </span>
            </td>

        </tr>

        <tr>

            <th>
                {{ $expiryDateLabel }}
                <br>
                <span class="label-en">
                    Date of Expiry
                </span>
            </th>

            <td>
                <span class="ltr-text">
                    {{ $familyCard->expiry_date?->format('Y-m-d') ?? '---' }}
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
        {{ $membersTitle }}
    </div>

    @if ($familyCard->members->isNotEmpty())

        <table class="members-table">

            <thead>

            <tr>

                <th class="number-column">
                    {{ $memberNumberLabel }}
                </th>

                <th class="name-column">
                    {{ $memberNameLabel }}
                    <br>
                    <span class="label-en">
                            Member Name
                        </span>
                </th>

                <th class="relationship-column">
                    {{ $relationshipLabel }}
                    <br>
                    <span class="label-en">
                            Relationship
                        </span>
                </th>

                <th class="status-column">
                    {{ $memberStatusLabel }}
                    <br>
                    <span class="label-en">
                            Status
                        </span>
                </th>

            </tr>

            </thead>

            <tbody>

            @foreach ($familyCard->members as $index => $member)

                @php
                    $citizen = $member->citizen;

                    $memberName = collect([
                        $citizen?->first_name,
                        $citizen?->father_name,
                        $citizen?->middle_name,
                        $citizen?->last_name,
                    ])
                        ->filter()
                        ->join(' ');

                    $memberName = $shapeArabic($memberName);

                    $relationship = match ($member->relationship) {
                        'spouse' => 'زوج / زوجة',
                        'child' => 'ابن / ابنة',
                        'father' => 'أب',
                        'mother' => 'أم',
                        'brother' => 'أخ',
                        'sister' => 'أخت',
                        'other' => 'أخرى',
                        default => 'غير محددة',
                    };

                    $relationship = $shapeArabic(
                        $relationship
                    );

                    $memberStatus = $member->is_active
                        ? $activeText
                        : $inactiveText;
                @endphp

                <tr>

                    <td class="number-column">
                        {{ $index + 1 }}
                    </td>

                    <td class="name-column">
                            <span class="arabic-text">
                                {{ $memberName ?: '---' }}
                            </span>
                    </td>

                    <td class="relationship-column">
                        {{ $relationship }}
                    </td>

                    <td class="status-column">

                            <span class="member-status">
                                {{ $memberStatus }}
                            </span>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    @else

        <div class="no-members">
            {{ $noMembersText }}
        </div>

    @endif

    <div class="footer-section">

        <div class="qr-title">
            {{ $qrTitle }}
            <span class="english-text">
                (QR / Code)
            </span>
        </div>

        <div class="qr-box">
            {{ $familyCard->qr_code ?: $familyCard->card_number }}
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
