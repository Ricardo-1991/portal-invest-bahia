@props([
    // Imagem de fundo, caminho RELATIVO a public/ (ex.: 'images/hero-rural-bahia.png').
    // Vira o `poster` quando há vídeo, ou a própria mídia quando não há.
    'poster',
    // Caminho do MP4, também relativo a public/ (ex.: 'videos/hero.mp4'). Opcional.
    'video' => null,
    // Classes extras na camada de mídia (ex.: 'opacity-55' nos heros de categoria).
    'mediaClass' => '',
    // Hero da primeira dobra: prioriza o download em vez de adiar.
    'eager' => false,
    // Texto alternativo. Vazio quando a imagem é puramente decorativa.
    'alt' => '',
])

@php
    // Só emite o <source> se o arquivo existir de fato. Sem essa checagem o
    // navegador pediria um MP4 inexistente e registraria um 404 no console
    // enquanto ninguém tivesse colocado o vídeo em public/videos/.
    $videoUrl = $video && is_file(public_path($video)) ? asset($video) : null;

    $posterUrl = asset($poster);

    // Versão .webp irmã (~90% menor que a PNG). Quando existe, é ela que serve.
    $webp = preg_replace('/\.(png|jpe?g)$/i', '.webp', $poster);
    $webpUrl = $webp !== $poster && is_file(public_path($webp)) ? asset($webp) : null;
@endphp

{{--
    Hero com mídia de fundo.

    O vídeo NÃO leva o atributo `autoplay`: quem dá play é o resources/js/app.js,
    e só quando o visitante não pediu redução de movimento. Consequência útil —
    enquanto o MP4 não existir, este bloco cai na imagem e o hero fica idêntico ao
    de antes. Assim que o arquivo aparecer na pasta, o vídeo passa a rodar sozinho,
    sem tocar em código.

    `muted` + `playsinline` são obrigatórios: sem eles o autoplay é bloqueado no
    iOS e no Chrome.
--}}
<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden']) }}>
    <div class="absolute inset-0 -z-10">
        @if ($videoUrl)
            {{-- O atributo `poster` aceita uma URL só, sem fallback: usa a webp quando existe. --}}
            <video data-hero-video
                   class="size-full object-cover {{ $mediaClass }}"
                   poster="{{ $webpUrl ?: $posterUrl }}"
                   muted loop playsinline preload="metadata"
                   aria-hidden="true" tabindex="-1">
                <source src="{{ $videoUrl }}" type="video/mp4">
            </video>
        @else
            <picture>
                @if ($webpUrl)
                    <source srcset="{{ $webpUrl }}" type="image/webp">
                @endif
                <img src="{{ $posterUrl }}" alt="{{ $alt }}"
                     @if ($eager) fetchpriority="high" @else loading="lazy" @endif
                     decoding="async"
                     class="size-full object-cover {{ $mediaClass }}">
            </picture>
        @endif

        {{-- Gradientes de leitura: cada página define os seus. --}}
        {{ $overlay ?? '' }}
    </div>

    {{ $slot }}
</section>
