<?php

return [
    'base_url' => env('CROSSCHEX_BASE_URL', 'https://api.us.crosschexcloud.com'),
    'api_key'  => env('CROSSCHEX_API_KEY'),
    'api_secret' => env('CROSSCHEX_API_SECRET'),

    // ventana de sincronización en minutos (por seguridad usamos 60)
    'sync_window_minutes' => env('CROSSCHEX_SYNC_WINDOW_MINUTES', 60),
];
