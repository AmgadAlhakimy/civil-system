@php
    $url = route(
        'passports.verify',
        ['passport' => $getRecord()->getKey()]
    );

    $options = new \chillerlan\QRCode\QROptions([
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'scale' => 5,
    ]);

    $qrCode = (new \chillerlan\QRCode\QRCode($options))
        ->render($url);
@endphp

<div style="display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 16px;">
    <img
        src="{{ $qrCode }}"
        alt="رمز التحقق من الجواز"
        width="240"
        height="240"
        style="display: block; max-width: 100%; height: auto;"
    >

    <span style="font-size: 13px; color: #78716c; text-align: center;">
        امسح الرمز لفتح صفحة التحقق من الجواز
    </span>
</div>
