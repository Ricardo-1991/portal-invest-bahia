@php
    $broker = $listing->user;
    // Conteúdo em português como base do documento interno.
    $title = $listing->getTranslation('title', 'pt', false);
    $subtitle = $listing->getTranslation('subtitle', 'pt', false);
    $description = $listing->getTranslation('description', 'pt', false);
@endphp
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #3a2515; font-size: 12px; }
        .brand { color: #a9862f; font-weight: bold; font-size: 20px; }
        h1 { font-size: 20px; margin: 4px 0; }
        .sub { color: #8a5a34; font-size: 13px; margin-bottom: 12px; }
        .meta td { padding: 4px 8px; }
        .label { color: #8a5a34; text-transform: uppercase; font-size: 9px; }
        .desc { margin-top: 12px; line-height: 1.5; white-space: pre-line; }
        .images img { width: 260px; height: 175px; object-fit: cover; margin: 4px; border: 1px solid #ecdccd; }
        .contact { margin-top: 18px; padding: 10px; background: #f7f1ec; }
        hr { border: none; border-top: 2px solid #c8a24a; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="brand">PIB - Portal Invest Bahia</div>
    <hr>

    <h1>{{ $title }}</h1>
    @if ($subtitle)<div class="sub">{{ $subtitle }}</div>@endif

    <table class="meta">
        <tr>
            <td><span class="label">Categoria</span><br>{{ ucfirst($listing->category) }}</td>
            @if ($listing->region)<td><span class="label">Região</span><br>{{ $listing->region }}</td>@endif
            @if ($listing->price)<td><span class="label">Valor</span><br>R$ {{ number_format((float) $listing->price, 2, ',', '.') }}</td>@endif
        </tr>
    </table>

    @if (! empty($images))
        <div class="images">
            @foreach ($images as $img)
                <img src="{{ $img }}" alt="">
            @endforeach
        </div>
    @endif

    <div class="desc">{{ $description }}</div>

    <div class="contact">
        <strong>Contato do corretor</strong><br>
        {{ $broker?->name }}<br>
        @if ($broker?->email_public ?: $broker?->email){{ $broker->email_public ?: $broker->email }}<br>@endif
        @if ($broker?->phone)Telefone: {{ $broker->phone }}<br>@endif
        @if ($broker?->whatsapp)WhatsApp: {{ $broker->whatsapp }}@endif
    </div>
</body>
</html>
