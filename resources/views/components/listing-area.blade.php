@props(['listing'])

@php
    // Sem casas decimais quando a área é inteira: "133 ha", mas "13,5 ha".
    $area = (float) $listing->area;
@endphp

{{-- Saída de texto puro, sem wrapper: quem chama decide a marcação em volta.
     Número e unidade ficam coladas de propósito (sem quebra de linha entre elas). --}}
@if ($area > 0){{ number_format($area, fmod($area, 1) === 0.0 ? 0 : 1, ',', '.') }} {{ __('site.listing.area_unit') }}@endif
