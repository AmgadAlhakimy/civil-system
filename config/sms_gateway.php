
<?php

return [
    'base_url' => env(
        'SMS_GATEWAY_BASE_URL',
        'https://app.sms-gateway.app/api/v1'
    ),

    'api_key' => env('SMS_GATEWAY_API_KEY'),
];
