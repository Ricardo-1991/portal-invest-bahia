<?php

namespace App\Support;

use InvalidArgumentException;

final class AreaUnits
{
    /** Metros quadrados por unidade. A variante do alqueire faz parte da unidade. */
    public const SQUARE_METERS = [
        'm2' => 1,
        'ha' => 10000,
        'alq_paulista' => 24200,
        'alq_norte' => 27225,
        'alq_mineiro' => 48400,
        'alq_baiano' => 96800,
    ];

    public static function toSquareMeters(int|float|string|null $area, ?string $unit): ?string
    {
        if ($area === null || $area === '') {
            return null;
        }

        $unit ??= 'ha';

        if (! array_key_exists($unit, self::SQUARE_METERS)) {
            throw new InvalidArgumentException("Unidade de área inválida: {$unit}");
        }

        return bcmul((string) $area, (string) self::SQUARE_METERS[$unit], 2);
    }
}
