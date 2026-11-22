<?php

declare(strict_types=1);

namespace Invoice;

use Invoice\Transport\Result;
use Invoice\Transport\TestTransport;
use PHPUnit\Framework\TestCase;

/**
 * Numbers and codes are sent to the API exactly as the caller gave them (as in 1.x).
 */
class PayloadTypesTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    private function send(Invoice $invoice): array
    {
        $transport = new TestTransport();
        $transport->setResponse(new Result(200, '{"status":"ok"}'));
        (new InvoiceApi('token', ['transport' => $transport]))->createInvoice($invoice);
        $payload = $transport->getPayload();
        self::assertNotNull($payload);
        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($data);
        self::assertIsArray($data['invoice']);

        return $data['invoice'];
    }

    public function testSettersAcceptNumericStringsAndIntCodes(): void
    {
        $invoice = (new Invoice())
            ->setNumber(2015001)
            ->setVariableSymbol(123213)
            ->setConstantSymbol('0308')
            ->setPrice('12.30')
            ->setPriceTotal(14.76)
            ->setClient((new Client())->setTaxNumber(2020)->setAddress((new Address())->setZip(81101)->setFax('555')))
            ->setDiscount(new Discount('percent', '5'))
            ->addItem((new InvoiceItem())->setQuantity('2')->setVat(20)->setPriceItem('6.15'));

        $data = $this->send($invoice);

        self::assertSame(2015001, $data['number']);
        self::assertSame(123213, $data['variable_symbol']);
        self::assertSame('0308', $data['constant_symbol']);
        self::assertSame('12.30', $data['price']);
        self::assertSame(14.76, $data['price_total']);
        self::assertSame(['type' => 'percent', 'value' => '5'], $data['discount']);
        self::assertIsArray($data['client']);
        self::assertSame(2020, $data['client']['tax_number']);
        self::assertIsArray($data['client']['address']);
        self::assertSame(81101, $data['client']['address']['zip']);
        self::assertSame('555', $data['client']['address']['fax']);
        self::assertIsArray($data['items']);
        self::assertIsArray($data['items'][0]);
        self::assertSame('2', $data['items'][0]['quantity']);
        self::assertSame(20, $data['items'][0]['vat']);
        self::assertSame('6.15', $data['items'][0]['price_item']);
    }

    public function testFromArrayKeepsValuesAsGiven(): void
    {
        $data = $this->send(Invoice::fromArray([
            'number' => 2015,
            'variable_symbol' => '0012345678',
            'price' => '12.30',
            'items' => [['quantity' => '2', 'price' => 2.3, 'description' => 'Item']],
        ]));

        self::assertSame(2015, $data['number']);
        self::assertSame('0012345678', $data['variable_symbol']);
        self::assertSame('12.30', $data['price']);
        self::assertSame([['vat' => null, 'quantity' => '2', 'description' => 'Item', 'price_item' => null, 'price' => 2.3, 'price_total' => null]], $data['items']);
    }

    public function testNonNumericPriceIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Invoice())->setPrice('12,30');
    }

    public function testNonNumericDiscountIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Discount('percent', 'ten');
    }

    public function testSetItemsIsChainable(): void
    {
        $data = $this->send((new Invoice())->setItems([(new InvoiceItem())->setPrice(1)])->setName('x'));

        self::assertSame('x', $data['name']);
        self::assertIsArray($data['items']);
        self::assertCount(1, $data['items']);
    }
}
