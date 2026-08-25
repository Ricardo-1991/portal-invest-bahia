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
    // Deixe whatsapp/phone vazios para simplesmente não exibir aquele canal.
    'contact' => [
        'email' => env('PIB_CONTACT_EMAIL', 'admin@pib.com.br'),
        'whatsapp' => env('PIB_CONTACT_WHATSAPP'),
        'phone' => env('PIB_CONTACT_PHONE'),
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
