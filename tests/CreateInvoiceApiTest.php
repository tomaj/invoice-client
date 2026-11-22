<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TestTransport;

final class CreateInvoiceApiTest extends TestCase
{
    public function testCreateInvoiceItem(): void
    {
        $transport = new TestTransport();
        $invoiceApi = new InvoiceApi('941240975320975', transport: $transport);
        $invoice = new Invoice();
        $invoiceItem = new InvoiceItem();
        $invoiceItem
            ->setQuantity(2)
            ->setPrice(2.3)
            ->setVat(20)
            ->setDiscount(new Discount(DiscountType::Percent, 20));
        $invoice
            ->setNumber('200011')
            ->setName('Invoice 200011')
            ->setDescription('invoice description')
            ->setVariableSymbol('123213')
            ->addItem($invoiceItem);

        $dummyResponse = '{"status": "ok", "id": "231r08iyfouihg3rt3r", "download_pdf_url": "http://download.com/", "html_url": "http://html.com/"}';
        $result = new Result(200, $dummyResponse);
        $transport->addResponse($result);

        $result = $invoiceApi->createInvoice($invoice);

        // check what client sent
        $payload = $transport->getLastRequest()['payload'] ?? null;
        self::assertNotNull($payload);
        self::assertJsonStringEqualsJsonString(json_encode(['invoice' => [
            'id' => null,
            'number' => '200011',
            'name' => 'Invoice 200011',
            'created' => null,
            'delivered' => null,
            'due' => null,
            'status' => null,
            'variable_symbol' => '123213',
            'constant_symbol' => null,
            'description' => 'invoice description',
            'items' => [
                [
                    'vat' => 20,
                    'quantity' => 2,
                    'description' => null,
                    'price_item' => null,
                    'price' => 2.3,
                    'price_total' => null,
                    'discount' => [
                        'type' => 'percent',
                        'value' => 20,
                    ],
                ],
            ],
            'price' => null,
            'price_total' => null,
            'currency' => null,
        ]], JSON_THROW_ON_ERROR), $payload);

        // check what client read
        self::assertSame('231r08iyfouihg3rt3r', $result->getId());
        self::assertSame('http://download.com/', $result->getDownloadUrl());
        self::assertSame('http://html.com/', $result->getHtmlUrl());
    }

    public function testCreateInvoiceErrorResponse(): void
    {
        $transport = new TestTransport();
        $invoiceApi = new InvoiceApi('941240975320975', transport: $transport);
        $transport->addResponse(new Result(400, '{"status": "error", "message": "Invalid invoice"}'));

        $result = $invoiceApi->createInvoice(new Invoice()->setName('Invoice 1'));

        self::assertFalse($result->isOk());
        self::assertSame('Invalid invoice', $result->getErrorMessage());
        self::assertNull($result->getId());
    }

    public function testCreateInvoiceUnparsableResponse(): void
    {
        $transport = new TestTransport();
        $invoiceApi = new InvoiceApi('941240975320975', transport: $transport);
        $transport->addResponse(new Result(500, 'Internal Server Error'));

        $result = $invoiceApi->createInvoice(new Invoice());

        self::assertFalse($result->isOk());
        self::assertSame('Cannot parse response from server (HTTP 500)', $result->getErrorMessage());
        self::assertSame(500, $result->getStatusCode());
        self::assertSame('Internal Server Error', $result->getBody());
    }
}
