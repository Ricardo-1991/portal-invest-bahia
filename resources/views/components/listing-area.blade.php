@props(['listing'])

@php
    $area = $listing->area;
    $unit = $listing->area_unit ?: 'ha';
    $decimals = str_contains((string) $area, '.')
        ? strlen(rtrim(substr((string) $area, strpos((string) $area, '.') + 1), '0'))
        : 0;
@endphp

@if ((float) $area > 0){{ number_format((float) $area, min($decimals, 2), ',', '.') }} {{ __('site.area_unit_short.'.$unit) }}@endif
