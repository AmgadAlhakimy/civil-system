<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 30px 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 8px;
            text-align: center;
        }

        .header p {
            font-size: 9px;
            margin: 3px 0;
            text-align: center;
        }

        .info {
            width: 100%;
            margin-bottom: 15px;
            direction: rtl;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .info-item {
            width: 25%;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .info-item .label {
            font-weight: bold;
            margin-bottom: 6px;
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
        }

        table.report th,
        table.report td {
            border: 1px solid #999;
            padding: 5px;
            vertical-align: middle;
        }

        table.report th {
            font-weight: bold;
            text-align: center;
        }

        table.report td {
            text-align: right;
        }

        .empty {
            text-align: center !important;
            padding: 20px !important;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
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

<table class="info">

    <tr>

        <td class="info-item">
            <div class="label">
                {{ $labels['report_name'] }}
            </div>

            <div class="value">
                {{ $reportName }}
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
                {{ $labels['records_count'] }}
            </div>

            <div class="value">
                {{ count($rows) }}
            </div>
        </td>

        <td class="info-item">
            <div class="label">
                {{ $labels['format'] }}
            </div>

            <div class="value">
                PDF
            </div>
        </td>

    </tr>

</table>

<table class="report">

    <thead>

    <tr>

        @foreach($headers as $header)

            <th>
                {{ $header }}
            </th>

        @endforeach

    </tr>

    </thead>

    <tbody>

    @forelse($rows as $row)

        <tr>

            @foreach($row as $value)

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

</body>

</html>
