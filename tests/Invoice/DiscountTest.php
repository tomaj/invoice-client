<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Invoice;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Exception\InvalidValueException;
use Tomaj\InvoiceClient\Exception\UnsupportedDiscountException;
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;

final class DiscountTest extends TestCase
{
    public function testValuesAreKeptAsGiven(): void
    {
        self::assertSame(DiscountType::Percent, new Discount(DiscountType::Percent, 10)->getType());
        self::assertSame(10, new Discount(DiscountType::Percent, 10)->getValue());
        self::assertSame(2.5, new Discount(DiscountType::Flat, 2.5)->getValue());
        self::assertSame('7.50', new Discount(DiscountType::Flat, '7.50')->getValue());
    }

    public function testNonNumericValueThrows(): void
    {
        try {
            new Discount(DiscountType::Percent, 'ten');
            self::fail('InvalidValueException expected');
        } catch (InvalidValueException $e) {
            self::assertSame("value must be a number or a numeric string, 'ten' given", $e->getMessage());
        }
    }

    public function testFromArray(): void
    {
        $discount = Discount::fromArray(['type' => 'flat', 'value' => '10']);
        self::assertSame(DiscountType::Flat, $discount->getType());
        self::assertSame(10.0, $discount->getValue());

        $default = Discount::fromArray([]);
        self::assertSame(DiscountType::None, $default->getType());
        self::assertSame(0.0, $default->getValue());

        self::assertSame(0.0, Discount::fromArray(['type' => 'percent', 'value' => 'abc'])->getValue());
    }

    public function testFromArrayRejectsUnknownTypes(): void
    {
        try {
            Discount::fromArray(['type' => 'half']);
            self::fail('InvalidValueException expected');
        } catch (InvalidValueException $e) {
            self::assertSame("Unknown discount type 'half'", $e->getMessage());
        }
    }

    public function testFlatDiscountIsOnlyForItems(): void
    {
        $flat = new Discount(DiscountType::Flat, 10);
        self::assertSame($flat, new InvoiceItem()->setDiscount($flat)->getDiscount());

        $this->expectException(UnsupportedDiscountException::class);
        new Invoice()->setDiscount($flat);
    }

    public function testInvoiceDiscountFromArray(): void
    {
        $invoice = Invoice::fromArray(['discount' => ['type' => 'percent', 'value' => 5]]);

        self::assertSame(DiscountType::Percent, $invoice->getDiscount()?->getType());
        self::assertSame(5.0, $invoice->getDiscount()->getValue());
    }
}
