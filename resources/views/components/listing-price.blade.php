@props([
    'listing',
    'size' => 'sm',
    'label' => true,
])

@php
    $amountClass = $size === 'lg' ? 'text-2xl md:text-3xl' : 'text-xl';
    $currencySymbol = $listing->currency === 'USD' ? 'US$' : 'R$';
@endphp

<div {{ $attributes }}>
    @if ($label)
        <span class="block text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">
            @lang('site.listing.price')
        </span>
    @endif

    <span class="block font-display font-medium leading-tight text-onyx-950 tabular {{ $label ? 'mt-1' : '' }} {{ $amountClass }}">
        @if ($listing->price)
            {{ $currencySymbol }} {{ \App\Support\MoneyInput::display($listing->price) }}
        @else
            @lang('site.listing.on_request')
        @endif
    </span>
</div>
