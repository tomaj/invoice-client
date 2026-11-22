<?php

declare(strict_types=1);

namespace Invoice;

class Currency
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const CZK = 'CZK';
    public const GBP = 'GBP';
    public const HUF = 'HUF';
    public const PLN = 'PLN';
    public const RUB = 'RUB';

    public static function isValid(mixed $currency): bool
    {
        return in_array($currency, [
            self::EUR,
            self::USD,
            self::CZK,
            self::GBP,
            self::HUF,
            self::PLN,
            self::RUB,
        ], true);
    }
}
