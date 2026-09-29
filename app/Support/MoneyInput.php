<?php

namespace App\Support;

final class MoneyInput
{
    /** Aceita números sem máscara ou a formatação brasileira exibida no formulário. */
    public static function toDecimal(int|float|string|null $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^\d+(?:\.\d{1,2})?$/', $value)) {
            return bcadd($value, '0', 2);
        }

        if (preg_match('/^\d{1,3}(?:\.\d{3})+(?:,\d{1,2})?$/', $value)) {
            return bcadd(str_replace(',', '.', str_replace('.', '', $value)), '0', 2);
        }

        if (preg_match('/^\d+(?:,\d{1,2})?$/', $value)) {
            return bcadd(str_replace(',', '.', $value), '0', 2);
        }

        // Retornar a entrada inválida permite à regra numeric exibir o erro normal do formulário.
        return $value;
    }

    public static function display(int|float|string|null $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $decimal = bcadd((string) $value, '0', 2);
        [$integer, $cents] = explode('.', $decimal);

        return number_format((int) $integer, 0, ',', '.').','.$cents;
    }
}
