<?php

declare(strict_types=1);

namespace Invoice;

use Invoice\Transport\Result;
use Invoice\Transport\TestTransport;
use PHPUnit\Framework\TestCase;

class CreateInvoiceApiTest extends TestCase
{
    public function testCreateInvoiceItem(): void
    {
        $transport = new TestTransport();
        $invoiceApi = new InvoiceApi('941240975320975', [
            'transport' => $transport,
        ]);
        $invoice = new Invoice();
        $invoiceItem = new InvoiceItem();
        $invoiceItem
            ->setQuantity(2)
            ->setPrice(2.3)
            ->setVat(20)
            ->setDiscount(new Discount('percent', 20));
        $invoice
            ->setNumber('200011')
            ->setName('Invoice 200011')
            ->setDescription('invoice description')
            ->setVariableSymbol('123213')
            ->addItem($invoiceItem);

        $dummyResponse = '{"status": "ok", "id": "231r08iyfouihg3rt3r", "download_pdf_url": "http://download.com/", "html_url": "http://html.com/"}';
        $result = new Result(200, $dummyResponse);
        $transport->setResponse($result);

        $result = $invoiceApi->createInvoice($invoice);

        // check what client sent
        $payload = $transport->getPayload();
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
        self::assertEquals('231r08iyfouihg3rt3r', $result->getId());
        self::assertEquals('http://download.com/', $result->getDownloadUrl());
        self::assertEquals('http://html.com/', $result->getHtmlUrl());
    }

    public function testCreateInvoiceErrorResponse(): void
    {
        $transport = new TestTransport();
        $invoiceApi = new InvoiceApi('941240975320975', [
            'transport' => $transport,
        ]);
        $transport->setResponse(new Result(400, '{"status": "error", "message": "Invalid invoice"}'));

        $result = $invoiceApi->createInvoice((new Invoice())->setName('Invoice 1'));

        self::assertFalse($result->isOk());
        self::assertSame('Invalid invoice', $result->getErrorMessage());
        self::assertNull($result->getId());
    }

    public function testCreateInvoiceUnparsableResponse(): void
    {
        $transport = new TestTransport();
        $invoiceApi = new InvoiceApi('941240975320975', [
            'transport' => $transport,
        ]);
        $transport->setResponse(new Result(500, 'Internal Server Error'));

        $result = $invoiceApi->createInvoice(new Invoice());

        self::assertFalse($result->isOk());
        self::assertSame('Cannot parse response from server', $result->getErrorMessage());
    }

    //    public function testGetInvoice(): void
    //    {
    //        $transport = new TestTransport();
    //        $invoiceApi = new InvoiceApi('941240975320975', [
    //            'transport' => $transport,
    //        ]);
    //
    //        $result = $invoiceApi->getInvoice('sadoywaf08yesfo8ysgreg');
    //
    //    }
    //
    //    public function testDeleteInvoice(): void
    //    {
    //        $transport = new TestTransport();
    //        $invoiceApi = new InvoiceApi('941240975320975', [
    //            'transport' => $transport,
    //        ]);
    //
    //        $result = $invoiceApi->deleteInvoice('sadoywaf08yesfo8ysgreg');
    //    }
}
