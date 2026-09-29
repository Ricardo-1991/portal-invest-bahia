<?php

namespace Tests\Unit;

use App\Support\AreaUnits;
use App\Support\ListingMap;
use App\Support\MoneyInput;
use PHPUnit\Framework\TestCase;

class AreaUnitsAndMoneyInputTest extends TestCase
{
    public function test_every_supported_area_unit_converts_to_square_meters(): void
    {
        $expected = [
            'm2' => '1.00',
            'ha' => '10000.00',
            'alq_paulista' => '24200.00',
            'alq_norte' => '27225.00',
            'alq_mineiro' => '48400.00',
            'alq_baiano' => '96800.00',
        ];

        foreach ($expected as $unit => $squareMeters) {
            $this->assertSame($squareMeters, AreaUnits::toSquareMeters('1', $unit));
        }

        $this->assertSame('242000.00', AreaUnits::toSquareMeters('2.50', 'alq_baiano'));
        $this->assertNull(AreaUnits::toSquareMeters(null, 'ha'));
    }

    public function test_map_urls_keep_the_exact_marker_and_reject_incomplete_coordinates(): void
    {
        $this->assertFalse(ListingMap::hasCoordinates('-14.788', null));
        $this->assertFalse(ListingMap::hasCoordinates('91', '-39.278'));
        $this->assertTrue(ListingMap::hasCoordinates('-14.788', '-39.278'));
        $this->assertStringContainsString('marker=-14.7880000%2C-39.2780000', ListingMap::embedUrl('-14.788', '-39.278'));
        $this->assertStringContainsString('mlat=-14.7880000', ListingMap::openUrl('-14.788', '-39.278'));
    }

    public function test_money_mask_accepts_whole_values_and_optional_cents(): void
    {
        $this->assertSame('65000000.00', MoneyInput::toDecimal('65000000'));
        $this->assertSame('65000000.00', MoneyInput::toDecimal('65.000.000,00'));
        $this->assertSame('65000000.25', MoneyInput::toDecimal('65.000.000,25'));
        $this->assertSame('65.000.000,00', MoneyInput::display('65000000.00'));
        $this->assertSame('invalid', MoneyInput::toDecimal('invalid'));
    }
}
