<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS)
    |--------------------------------------------------------------------------
    |
    | Define quais origens, métodos e cabeçalhos são permitidos nas requisições
    | cross-origin. Restrinja allowed_origins ao domínio real do frontend em
    | produção — nunca use '*' em APIs autenticadas.
    |
    | Configure FRONTEND_URL no .env para a origem do seu frontend.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // Lê as origens permitidas do .env; sem fallback para '*'.
    // Ex.: CORS_ALLOWED_ORIGINS=https://app.seudominio.com
    'allowed_origins' => array_filter(
        explode(',', env('CORS_ALLOWED_ORIGINS', env('FRONTEND_URL', '')))
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'Accept', 'X-Requested-With'],

    'exposed_headers' => [],

    // Credenciais (cookies, Authorization) precisam de allowed_origins explícito, não '*'
    'supports_credentials' => false,

    'max_age' => 86400,

];
