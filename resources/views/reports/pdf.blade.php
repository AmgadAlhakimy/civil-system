<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 landscape;
            margin: 25px 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 9px;
            unicode-bidi: embed;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 17px;
            margin: 0 0 7px;
            text-align: center;
        }

        .header p {
            font-size: 9px;
            margin: 3px 0;
            text-align: center;
        }

        .info {
            width: 100%;
            margin-bottom: 12px;
            direction: rtl;
            unicode-bidi: embed;
            border-collapse: separate;
            border-spacing: 6px;
        }

        .info-item {
            width: 25%;
            padding: 7px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .info-item .label {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        .info-item .value {
            text-align: center;
            direction: rtl;
            unicode-bidi: embed;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
            unicode-bidi: embed;
            table-layout: fixed;
        }

        table.report thead {
            display: table-header-group;
        }

        table.report tbody {
            direction: rtl;
        }

        table.report tr {
            direction: rtl;
            page-break-inside: avoid;
        }

        table.report th,
        table.report td {
            border: 1px solid #999;
            padding: 4px 3px;
            vertical-align: middle;
            direction: rtl;
            unicode-bidi: embed;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table.report th {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2;
        }

        table.report td {
            font-size: 8px;
            text-align: right;
        }

        .empty {
            text-align: center !important;
            padding: 18px !important;
        }

        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 9px;
        }

        .page-number {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>

<body>

<div class="header">

    <h1>
        {{ $title }}
    </h1>

    <p>
        {{ $labels['report_date'] }}:
        {{ now()->format('Y-m-d H:i') }}
    </p>

</div>

<table class="info" dir="rtl">

    <tr>

        <td class="info-item">
            <div class="label">
                {{ $labels['format'] }}
            </div>

            <div class="value">
                PDF
            </div>
        </td>

        <td class="info-item">
            <div class="label">
                {{ $labels['records_count'] }}
            </div>

            <div class="value">
                {{ count($rows) }}
            </div>
        </td>

        <td class="info-item">
            <div class="label">
                {{ $labels['report_type'] }}
            </div>

            <div class="value">
                {{ $title }}
            </div>
        </td>

        <td class="info-item">
            <div class="label">
                {{ $labels['report_name'] }}
            </div>

            <div class="value">
                {{ $reportName }}
            </div>
        </td>

    </tr>

</table>

<table class="report" dir="rtl">

    <thead>

    <tr>

        @foreach(array_reverse($headers) as $header)

            <th>
                {{ $header }}
            </th>

        @endforeach

    </tr>

    </thead>

    <tbody>

    @forelse($rows as $row)

        <tr>

            @foreach(array_reverse($row) as $value)

                <td>
                    {{ $value ?? '-' }}
                </td>

            @endforeach

        </tr>

    @empty

        <tr>

            <td
                colspan="{{ count($headers) }}"
                class="empty"
            >
                {{ $labels['no_data'] }}
            </td>

        </tr>

    @endforelse

    </tbody>

</table>

<div class="footer">

    {{ $labels['total_records'] }}:
    {{ count($rows) }}

</div>

<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont("DejaVu Sans", "normal");
        $size = 8;

        $text = "صفحة {PAGE_NUM} من {PAGE_COUNT}";

        $width = $fontMetrics->getTextWidth(
            $text,
            $font,
            $size
        );

        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 20;

        $pdf->page_text(
            $x,
            $y,
            $text,
            $font,
            $size,
            array(0, 0, 0)
        );
    }
</script>

</body>

</html>
