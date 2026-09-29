<?php

namespace App\Support;

final class ListingMap
{
    public static function hasCoordinates(int|float|string|null $latitude, int|float|string|null $longitude): bool
    {
        return is_numeric($latitude) && is_numeric($longitude)
            && (float) $latitude >= -90 && (float) $latitude <= 90
            && (float) $longitude >= -180 && (float) $longitude <= 180;
    }

    public static function embedUrl(int|float|string $latitude, int|float|string $longitude): string
    {
        $lat = (float) $latitude;
        $lon = (float) $longitude;
        $box = implode(',', [
            self::coordinate(max(-180, $lon - 0.01)),
            self::coordinate(max(-90, $lat - 0.01)),
            self::coordinate(min(180, $lon + 0.01)),
            self::coordinate(min(90, $lat + 0.01)),
        ]);

        return 'https://www.openstreetmap.org/export/embed.html?'.http_build_query([
            'bbox' => $box,
            'layer' => 'mapnik',
            'marker' => self::coordinate($lat).','.self::coordinate($lon),
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public static function openUrl(int|float|string $latitude, int|float|string $longitude): string
    {
        $lat = self::coordinate((float) $latitude);
        $lon = self::coordinate((float) $longitude);

        return 'https://www.openstreetmap.org/?'.http_build_query([
            'mlat' => $lat,
            'mlon' => $lon,
        ], '', '&', PHP_QUERY_RFC3986).'#map=15/'.$lat.'/'.$lon;
    }

    private static function coordinate(float $value): string
    {
        return number_format($value, 7, '.', '');
    }
}
