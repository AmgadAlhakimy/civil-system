<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من الجواز | السجل المدني</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Tahoma, Arial, sans-serif;
            background: #fffbeb;
            color: #451a03;
            direction: rtl;
        }

        .container {
            width: 100%;
            max-width: 650px;
            margin: 30px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin-bottom: 8px;
            color: #92400e;
            font-size: 27px;
        }

        .header p {
            color: #78716c;
            line-height: 1.8;
        }

        .card {
            background: #ffffff;
            border: 1px solid #fde68a;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 8px 25px rgba(69, 26, 3, 0.07);
        }

        .status {
            text-align: center;
            padding: 16px;
            margin-bottom: 25px;
            border-radius: 12px;
            font-size: 19px;
            font-weight: bold;
        }

        .valid {
            background: #dcfce7;
            color: #166534;
        }

        .invalid {
            background: #fee2e2;
            color: #991b1b;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .detail {
            padding: 15px;
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 10px;
            min-width: 0;
        }

        .label {
            display: block;
            margin-bottom: 9px;
            color: #78716c;
            font-size: 13px;
        }

        .value {
            display: block;
            font-size: 16px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .footer {
            text-align: center;
            color: #78716c;
            font-size: 13px;
            margin-top: 24px;
            line-height: 1.8;
        }

        @media (max-width: 480px) {
            .details {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 18px;
            }

            .header h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
<main class="container">
    <header class="header">
        <h1>السجل المدني</h1>
        <p>خدمة التحقق الإلكتروني من جوازات السفر</p>
    </header>

    <section class="card">
        <div class="status {{ $isValid ? 'valid' : 'invalid' }}">
            {{ $isValid ? 'الجواز ساري المفعول' : 'الجواز غير ساري المفعول' }}
        </div>

        <div class="details">
            <div class="detail">
                <span class="label">رقم الجواز</span>
                <span class="value">{{ $passport->passport_number }}</span>
            </div>

            <div class="detail">
                <span class="label">نوع الجواز</span>
                <span class="value">
                        {{ match ($passport->type) {
                            'ordinary' => 'عادي',
                            'diplomatic' => 'دبلوماسي',
                            'official' => 'رسمي',
                            default => 'غير محدد',
                        } }}
                    </span>
            </div>

            <div class="detail">
                <span class="label">حالة الجواز</span>
                <span class="value">
                        {{ match ($passport->status) {
                            'pending' => 'قيد الانتظار',
                            'rejected' => 'مرفوض',
                            'active' => 'ساري',
                            'expired' => 'منتهي',
                            'cancelled' => 'ملغي',
                            'lost' => 'مفقود',
                            'damaged' => 'تالف',
                            default => 'غير محدد',
                        } }}
                    </span>
            </div>

            <div class="detail">
                <span class="label">تاريخ الإصدار</span>
                <span class="value">
                        {{ $passport->issue_date?->format('d/m/Y') ?? 'غير محدد' }}
                    </span>
            </div>

            <div class="detail">
                <span class="label">تاريخ الانتهاء</span>
                <span class="value">
                        {{ $passport->expiry_date?->format('d/m/Y') ?? 'غير محدد' }}
                    </span>
            </div>
        </div>
    </section>

    <footer class="footer">
        هذه الصفحة مخصصة للتحقق الإلكتروني من حالة الجواز.
        <br>
        لا تُعد هذه الصفحة بديلًا عن الوثيقة الرسمية.
    </footer>
</main>
</body>
</html>
