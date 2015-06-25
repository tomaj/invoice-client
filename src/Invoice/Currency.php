<?php

namespace Invoice;

class Currency
{
    const EUR = 'EUR';
    const USD = 'USD';
    const CZK = 'CZK';
    const GBP = 'GBP';
    const HUF = 'HUF';
    const PLN = 'PLN';
    const RUB = 'RUB';

    public static function isValid($currency)
    {
        return in_array($currency, [
            self::EUR,
            self::USD,
            self::CZK,
            self::GBP,
            self::HUF,
            self::PLN,
            self::RUB,
        ]);
    }
}
