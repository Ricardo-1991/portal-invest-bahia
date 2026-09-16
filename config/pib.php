<?php

return [
    // Idiomas suportados no site público (ordem = ordem no seletor).
    'locales' => [
        'pt' => 'Português',
        'en' => 'English',
        'es' => 'Español',
        'it' => 'Italiano',
    ],

    // Categorias de classificados => rótulo padrão (PT) / chave da página.
    'categories' => [
        'fazenda' => 'Fazendas',
        'ativo' => 'Ativos',
        'servico' => 'Serviços',
    ],

    // Canais de contato exibidos na página pública de Contatos.
    // Use PIB_CONTACT_PHONES, separado por vírgulas, para substituir os telefones.
    'contact' => [
        'email' => env('PIB_CONTACT_EMAIL', 'isaacambiental@gmail.com'),
        'whatsapp' => env('PIB_CONTACT_WHATSAPP', '+55 (73) 99802-8065'),
        'phones' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('PIB_CONTACT_PHONES', '+55 (73) 99802-8065, +55 (73) 98188-2332, +55 (71) 99199-6668')),
        ))),
    ],

    // Limites do hero. O transporte aceita uma pequena margem para que o
    // Filament consiga exibir a validação amigável dos 100 MB ao usuário.
    'uploads' => [
        'hero_video_max_kb' => (int) env('PIB_HERO_VIDEO_MAX_KB', 102400),
        'temporary_max_kb' => (int) env('PIB_TEMPORARY_UPLOAD_MAX_KB', 122880),
    ],

    // Nota: TRUSTED_PROXIES é lido diretamente via env() em bootstrap/app.php
    // (config() ainda não está disponível nesse estágio do bootstrap).

    // Credenciais criadas pelo DatabaseSeeder. Em produção, defina
    // PIB_ADMIN_PASSWORD e PIB_BROKER_PASSWORD no .env — o seeder recusa
    // rodar em produção com a senha padrão de desenvolvimento.
    'seed' => [
        'admin_email' => env('PIB_ADMIN_EMAIL', 'admin@pib.com.br'),
        'admin_password' => env('PIB_ADMIN_PASSWORD', 'password'),
        'broker_email' => env('PIB_BROKER_EMAIL', 'corretor@pib.com.br'),
        'broker_password' => env('PIB_BROKER_PASSWORD', 'password'),
    ],
];
