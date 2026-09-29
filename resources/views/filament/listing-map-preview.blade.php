@php
    $mapUrl = \App\Support\ListingMap::embedUrl($latitude, $longitude);
    $openUrl = \App\Support\ListingMap::openUrl($latitude, $longitude);
@endphp

<div class="space-y-2">
    <iframe src="{{ $mapUrl }}" title="Prévia da localização no OpenStreetMap"
            class="w-full rounded-lg border border-gray-200" style="height: 280px"
            loading="lazy" referrerpolicy="no-referrer" sandbox="allow-scripts allow-same-origin"></iframe>
    <a href="{{ $openUrl }}" target="_blank" rel="noopener noreferrer" class="text-sm underline">
        Abrir no OpenStreetMap
    </a>
</div>
