@props([
    // Imagem de fundo, caminho RELATIVO a public/ (ex.: 'images/hero-rural-bahia.png').
    // Vira o `poster` quando há vídeo, ou a própria mídia quando não há.
    'poster',
    // Caminho do MP4, também relativo a public/ (ex.: 'videos/hero.mp4'). Opcional.
    'video' => null,
    // URL pública de uma mídia administrável (por exemplo, Media Library).
    'videoUrl' => null,
    'videoType' => 'video/mp4',
    // O hero pode preservar o quadro inteiro do vídeo sem recorte.
    'videoFit' => 'cover',
    // Classes extras na camada de mídia (ex.: 'opacity-55' nos heros de categoria).
    'mediaClass' => '',
    // Hero da primeira dobra: prioriza o download em vez de adiar.
    'eager' => false,
    // Texto alternativo. Vazio quando a imagem é puramente decorativa.
    'alt' => '',
])

@php
    // Uma URL da Media Library tem prioridade. O caminho estático legado só é
    // emitido quando o arquivo realmente existe em public/.
    $resolvedVideoUrl = $videoUrl ?: ($video && is_file(public_path($video)) ? asset($video) : null);
    $videoFitClass = $videoFit === 'contain' ? 'object-contain' : 'object-cover';

    $posterUrl = asset($poster);

    // Versão .webp irmã (~90% menor que a PNG). Quando existe, é ela que serve.
    $webp = preg_replace('/\.(png|jpe?g)$/i', '.webp', $poster);
    $webpUrl = $webp !== $poster && is_file(public_path($webp)) ? asset($webp) : null;
@endphp

{{--
    Hero com mídia de fundo.

    O vídeo NÃO leva o atributo `autoplay`: quem dá play é o resources/js/app.js,
    e só quando o visitante não pediu redução de movimento. Consequência útil —
    enquanto nenhum MP4 estiver cadastrado, este bloco cai na imagem e mantém o
    poster. Após o upload, o JavaScript inicia o vídeo automaticamente.

    `muted` + `playsinline` são obrigatórios: sem eles o autoplay é bloqueado no
    iOS e no Chrome.
--}}
<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden']) }}>
    <div class="absolute inset-0 -z-10">
        @if ($resolvedVideoUrl)
            {{-- O atributo `poster` aceita uma URL só, sem fallback: usa a webp quando existe. --}}
            <video data-hero-video
                   class="size-full {{ $videoFitClass }} {{ $mediaClass }}"
                   poster="{{ $webpUrl ?: $posterUrl }}"
                   muted loop playsinline preload="metadata"
                   aria-hidden="true" tabindex="-1">
                <source src="{{ $resolvedVideoUrl }}" type="{{ $videoType }}">
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
