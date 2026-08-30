<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PortalBrandWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected static ?int $sort = -2;

    protected string $view = 'filament.widgets.portal-brand-widget';
}
