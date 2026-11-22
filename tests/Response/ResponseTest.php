<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Response;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Response\CreateInvoiceResponse;
use Tomaj\InvoiceClient\Response\DeleteInvoiceResponse;
use Tomaj\InvoiceClient\Response\InvoiceDetailResponse;
use Tomaj\InvoiceClient\Transport\Result;

final class ResponseTest extends TestCase
{
    /**
     * @return iterable<string, array{Result|null, bool, string|null}>
     */
    public static function results(): iterable
    {
        yield 'ok' => [new Result(200, '{"status":"ok"}'), true, null];
        yield 'ok 201' => [new Result(201, '{"status":"ok"}'), true, null];
        yield 'ok 299' => [new Result(299, '{"status":"ok"}'), true, null];
        yield 'api error with 200' => [new Result(200, '{"status":"error","message":"Invalid VAT"}'), false, 'Invalid VAT'];
        yield '401' => [new Result(401, '{"status":"error","message":"Invalid token"}'), false, 'Invalid token'];
        yield '404 without message' => [new Result(404, '{"status":"error"}'), false, 'Unknown error'];
        yield 'empty message' => [new Result(400, '{"status":"error","message":""}'), false, 'Unknown error'];
        yield 'non-string message' => [new Result(400, '{"status":"error","message":["x"]}'), false, 'Unknown error'];
        yield 'status ok with 500' => [new Result(500, '{"status":"ok"}'), false, 'Unexpected HTTP status 500'];
        yield 'status ok with 199' => [new Result(199, '{"status":"ok"}'), false, 'Unexpected HTTP status 199'];
        yield 'status ok with 300' => [new Result(300, '{"status":"ok"}'), false, 'Unexpected HTTP status 300'];
        yield 'status ok with 500 and message' => [new Result(500, '{"status":"ok","message":"Partial failure"}'), false, 'Partial failure'];
        yield 'missing status' => [new Result(200, '{"id":"1"}'), false, 'Unknown error'];
        yield 'html' => [new Result(502, '<html>Bad gateway</html>'), false, 'Cannot parse response from server (HTTP 502)'];
        yield 'json list' => [new Result(200, '[{"status":"ok"}]'), false, 'Cannot parse response from server (HTTP 200)'];
        yield 'empty object' => [new Result(200, '{}'), false, 'Cannot parse response from server (HTTP 200)'];
        yield 'nothing sent' => [null, false, 'No request was sent'];
    }

    #[DataProvider('results')]
    public function testOkAndErrorMessage(?Result $result, bool $ok, ?string $error): void
    {
        foreach ([new CreateInvoiceResponse($result), new InvoiceDetailResponse($result), new DeleteInvoiceResponse($result)] as $response) {
            self::assertSame($ok, $response->isOk());
            self::assertSame($error, $response->getErrorMessage());
            self::assertSame($result?->getCode() ?? 0, $response->getStatusCode());
            self::assertSame($result?->getBody() ?? '', $response->getBody());
        }
    }

    public function testData(): void
    {
        $response = new InvoiceDetailResponse(new Result(200, '{"status":"ok","invoice":{"id":5}}'));

        self::assertSame(['status' => 'ok', 'invoice' => ['id' => 5]], $response->getData());
        self::assertSame([], new InvoiceDetailResponse(new Result(200, '[1,2]'))->getData());
        self::assertSame([], new InvoiceDetailResponse(new Result(200, '"text"'))->getData());
    }

    public function testCreatedInvoice(): void
    {
        $response = new CreateInvoiceResponse(new Result(200, '{"status":"ok","id":15,"html_url":"https://x/html","download_pdf_url":"https://x/pdf"}'));

        self::assertSame('15', $response->getId());
        self::assertSame('https://x/html', $response->getHtmlUrl());
        self::assertSame('https://x/pdf', $response->getDownloadUrl());
    }

    public function testCreatedInvoiceWithUnexpectedTypes(): void
    {
        $response = new CreateInvoiceResponse(new Result(200, '{"status":"ok","id":1.5,"html_url":null,"download_pdf_url":["x"]}'));

        self::assertTrue($response->isOk());
        self::assertNull($response->getId());
        self::assertNull($response->getHtmlUrl());
        self::assertNull($response->getDownloadUrl());
    }

    public function testFailedCreateHasNoIdOrUrls(): void
    {
        $response = new CreateInvoiceResponse(new Result(500, '{"status":"ok","id":"1","html_url":"h","download_pdf_url":"p"}'));

        self::assertNull($response->getId());
        self::assertNull($response->getHtmlUrl());
        self::assertNull($response->getDownloadUrl());
    }
}
