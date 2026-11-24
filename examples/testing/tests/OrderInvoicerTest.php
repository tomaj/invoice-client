<?php

declare(strict_types=1);

namespace App\Tests;

use App\InvoicingFailed;
use App\OrderInvoicer;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;
use Tomaj\InvoiceClient\Transport\HttpMethod;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TestTransport;

final class OrderInvoicerTest extends TestCase
{
    private const array ORDER = [
        'number' => '2026006',
        'lines' => [
            ['product' => 'Coffee beans 1 kg', 'quantity' => 2, 'price' => '18.50'],
            ['product' => 'Grinder', 'quantity' => 1, 'price' => '89.00'],
        ],
    ];

    public function testReturnsThePdfLinkOfTheNewInvoice(): void
    {
        $transport = new TestTransport()->addResponse(new Result(200, (string) json_encode([
            'status' => 'ok',
            'id' => 'inv-1',
            'html_url' => 'https://invoices.test/html/inv-1',
            'download_pdf_url' => 'https://invoices.test/pdf-download/inv-1',
        ])));
        $invoicer = new OrderInvoicer(new InvoiceApi('test-token', transport: $transport));

        self::assertSame('https://invoices.test/pdf-download/inv-1', $invoicer->invoice(self::ORDER));
    }

    public function testSendsTheOrderLinesAsInvoiceItems(): void
    {
        $transport = new TestTransport()->addResponse(new Result(200, '{"status":"ok","id":"inv-1"}'));
        new OrderInvoicer(new InvoiceApi('test-token', transport: $transport))->invoice(self::ORDER);

        $request = $transport->getLastRequest();
        self::assertNotNull($request);
        self::assertSame(HttpMethod::Post, $request['method']);
        self::assertSame('https://chcemfakturu.sk/api/v1/invoices/create', $request['url']);
        self::assertSame('test-token', $request['apiToken']);

        $payload = json_decode((string) $request['payload'], true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('2026006', $payload['invoice']['variable_symbol']);
        self::assertSame('EUR', $payload['invoice']['currency']);
        self::assertSame(
            [['Coffee beans 1 kg', 2, '18.50'], ['Grinder', 1, '89.00']],
            array_map(fn (array $item) => [$item['description'], $item['quantity'], $item['price_item']], $payload['invoice']['items']),
        );
    }

    public function testAnApiErrorBecomesAnApplicationException(): void
    {
        $transport = new TestTransport()->addResponse(new Result(422, '{"status":"error","message":"Invalid VAT number"}'));
        $invoicer = new OrderInvoicer(new InvoiceApi('test-token', transport: $transport));

        $this->expectException(InvoicingFailed::class);
        $this->expectExceptionMessage('Order 2026006: Invalid VAT number (HTTP 422)');
        $invoicer->invoice(self::ORDER);
    }

    public function testWithoutAQueuedResponseNothingIsSent(): void
    {
        $invoicer = new OrderInvoicer(new InvoiceApi('test-token', transport: new TestTransport()));

        $this->expectExceptionMessage('Order 2026006: No request was sent (HTTP 0)');
        $invoicer->invoice(self::ORDER);
    }

    public function testTheHttpRequestThroughGuzzleMockHandler(): void
    {
        // one level lower: the real GuzzleTransport with a mocked Guzzle handler, to check the HTTP request itself
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","id":"inv-2","download_pdf_url":"https://invoices.test/pdf-download/inv-2"}'),
        ]));
        $stack->push(Middleware::history($history));
        $api = new InvoiceApi('test-token', transport: new GuzzleTransport(new Client(['handler' => $stack])));

        self::assertSame('https://invoices.test/pdf-download/inv-2', new OrderInvoicer($api)->invoice(self::ORDER));

        self::assertCount(1, $history);
        $request = $history[0]['request'];
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer test-token', $request->getHeaderLine('Authorization'));
        self::assertStringStartsWith('{"invoice":', (string) $request->getBody());
    }
}
