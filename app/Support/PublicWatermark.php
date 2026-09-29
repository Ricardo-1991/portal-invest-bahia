<?php

namespace App\Support;

use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Unit;
use Spatie\MediaLibrary\Conversions\Conversion;

final class PublicWatermark
{
    public static function apply(Conversion $conversion): Conversion
    {
        return $conversion->watermark(
            public_path('images/marca_dagua.png'),
            position: AlignPosition::BottomRight,
            paddingX: 3,
            paddingY: 3,
            paddingUnit: Unit::Percent,
            width: 18,
            widthUnit: Unit::Percent,
            alpha: 65,
        );
    }
}
