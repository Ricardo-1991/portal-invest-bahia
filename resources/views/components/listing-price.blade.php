@props([
    'listing',
    // 'sm' no card vertical, 'lg' no card horizontal e na página de detalhe.
    'size' => 'sm',
    // false quando quem chama já tem o próprio rótulo (ex.: o <dt> da ficha técnica).
    'label' => true,
])

@php
    $perHectare = $listing->pricePerHectare();

    $amountClass = $size === 'lg' ? 'text-2xl md:text-3xl' : 'text-xl';
    $perHectareClass = $size === 'lg' ? 'text-sm' : 'text-xs';
@endphp

{{--
    Bloco de valor unificado — antes cada card formatava o preço do seu jeito
    (rótulo "Valor" vs "Venda", ouro vs onyx, text-lg vs text-2xl).

    Atenção: tests/Feature/PublicSiteTest.php faz assert nas strings literais
    'R$ 2.660.000,00' e 'Hectare: R$ 20.000,00'. O prefixo do hectare e o
    formato BRL precisam continuar saindo exatamente assim.
--}}
<div {{ $attributes }}>
    @if ($label)
        <span class="block text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">
            @lang('site.listing.price')
        </span>
    @endif

    <span class="block font-display font-medium leading-tight text-onyx-950 tabular {{ $label ? 'mt-1' : '' }} {{ $amountClass }}">
        @if ($listing->price)
            R$ {{ number_format((float) $listing->price, 2, ',', '.') }}
        @else
            @lang('site.listing.on_request')
        @endif
    </span>

    @if ($perHectare)
        <span class="mt-1 block text-onyx-500 tabular {{ $perHectareClass }}">
            @lang('site.listing.per_hectare'): R$ {{ number_format($perHectare, 2, ',', '.') }}
        </span>
    @endif
</div>
