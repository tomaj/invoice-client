<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Tomaj\InvoiceClient\Exception\InvalidValueException;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;
use Tomaj\InvoiceClient\Transport\HttpMethod;
use Tomaj\InvoiceClient\Transport\NullTransport;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TestTransport;
use Tomaj\InvoiceClient\Transport\TransportInterface;

final class InvoiceApiTest extends TestCase
{
    public function testDefaults(): void
    {
        $api = new InvoiceApi('token');

        self::assertSame('https://chcemfakturu.sk/html/abc', $api->htmlUrl('abc'));
        self::assertInstanceOf(GuzzleTransport::class, new \ReflectionProperty(InvoiceApi::class, 'transport')->getValue($api));
    }

    public function testTargetTrailingSlashIsRemoved(): void
    {
        $api = new InvoiceApi('token', 'http://localhost:8080///');

        self::assertSame('http://localhost:8080/html/1', $api->htmlUrl('1'));
        self::assertSame('http://localhost:8080/pdf/1', $api->pdfUrl('1'));
        self::assertSame('http://localhost:8080/pdf-download/1', $api->pdfDownloadUrl('1'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidTargets(): iterable
    {
        yield 'no scheme' => ['chcemfakturu.sk'];
        yield 'ftp' => ['ftp://chcemfakturu.sk'];
        yield 'empty' => [''];
        yield 'scheme not at the start' => ['xhttps://chcemfakturu.sk'];
    }

    #[DataProvider('invalidTargets')]
    public function testInvalidTargetThrows(string $target): void
    {
        try {
            new InvoiceApi('token', $target);
            self::fail('InvalidValueException expected');
        } catch (InvalidValueException $e) {
            self::assertSame(sprintf("Target must start with http:// or https://, '%s' given", $target), $e->getMessage());
        }
    }

    public function testIdsAreUrlEncoded(): void
    {
        $transport = new TestTransport();
        $api = new InvoiceApi('token', 'https://api.test', $transport);

        self::assertSame('https://api.test/pdf/a%2F..%2Fb%3Fx%3D1', $api->pdfUrl('a/../b?x=1'));
        $api->getInvoice('a/b');
        self::assertSame('https://api.test/api/v1/invoices/detail/a%2Fb', $transport->getLastRequest()['url'] ?? null);
    }

    public function testGetInvoice(): void
    {
        $transport = new TestTransport()->addResponse(new Result(200, '{"status":"ok","invoice":{"id":"abc","number":"2024001"}}'));
        $api = new InvoiceApi('token', 'https://api.test', $transport);

        $response = $api->getInvoice('abc');

        self::assertTrue($response->isOk());
        self::assertSame(['status' => 'ok', 'invoice' => ['id' => 'abc', 'number' => '2024001']], $response->getData());
        self::assertSame([['method' => HttpMethod::Get, 'url' => 'https://api.test/api/v1/invoices/detail/abc', 'apiToken' => 'token', 'payload' => null]], $transport->getRequests());
    }

    public function testDeleteInvoice(): void
    {
        $transport = new TestTransport()->addResponse(new Result(404, '{"status":"error","message":"Invoice not found"}'));
        $api = new InvoiceApi('token', 'https://api.test', $transport);

        $response = $api->deleteInvoice('missing');

        self::assertFalse($response->isOk());
        self::assertSame(404, $response->getStatusCode());
        self::assertSame('Invoice not found', $response->getErrorMessage());
        self::assertSame([['method' => HttpMethod::Delete, 'url' => 'https://api.test/api/v1/invoices/delete/missing', 'apiToken' => 'token', 'payload' => null]], $transport->getRequests());
    }

    public function testNullTransportGivesAFailedResponse(): void
    {
        $response = new InvoiceApi('token', transport: new NullTransport())->createInvoice(new Invoice());

        self::assertFalse($response->isOk());
        self::assertSame(0, $response->getStatusCode());
        self::assertSame('No request was sent', $response->getErrorMessage());
    }

    public function testRequestsAndResponsesAreLoggedWithoutTheTokenOrPayload(): void
    {
        $logger = new TestLogger();
        $transport = new TestTransport()
            ->addResponse(new Result(200, '{"status":"ok","id":"1"}'))
            ->addResponse(new Result(422, '{"status":"error","message":"Invalid VAT"}'));
        $api = new InvoiceApi('secret-token', 'https://api.test', $transport, $logger);

        $api->createInvoice(new Invoice()->setName('Personal data'));
        $api->deleteInvoice('7');

        self::assertSame([
            [LogLevel::DEBUG, 'Invoice API request', ['method' => 'POST', 'url' => 'https://api.test/api/v1/invoices/create']],
            [LogLevel::INFO, 'Invoice API response', ['method' => 'POST', 'url' => 'https://api.test/api/v1/invoices/create', 'status' => 200]],
            [LogLevel::DEBUG, 'Invoice API request', ['method' => 'DELETE', 'url' => 'https://api.test/api/v1/invoices/delete/7']],
            [LogLevel::WARNING, 'Invoice API error: {error}', ['method' => 'DELETE', 'url' => 'https://api.test/api/v1/invoices/delete/7', 'status' => 422, 'error' => 'Invalid VAT']],
        ], $logger->records);
        $serialized = serialize($logger->records);
        self::assertStringNotContainsString('secret-token', $serialized);
        self::assertStringNotContainsString('Personal data', $serialized);
    }

    public function testTransportExceptionIsLoggedAndRethrown(): void
    {
        $logger = new TestLogger();
        $exception = new TransportException('GET https://api.test/api/v1/invoices/detail/1 failed: timeout');
        $transport = new readonly class ($exception) implements TransportInterface {
            public function __construct(private TransportException $exception) {}

            #[\Override]
            public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result
            {
                throw $this->exception;
            }
        };
        $api = new InvoiceApi('token', 'https://api.test', $transport, $logger);

        try {
            $api->getInvoice('1');
            self::fail('TransportException expected');
        } catch (TransportException $e) {
            self::assertSame($exception, $e);
        }
        self::assertSame([
            [LogLevel::DEBUG, 'Invoice API request', ['method' => 'GET', 'url' => 'https://api.test/api/v1/invoices/detail/1']],
            [LogLevel::ERROR, 'Invoice API request failed', ['method' => 'GET', 'url' => 'https://api.test/api/v1/invoices/detail/1', 'exception' => $exception]],
        ], $logger->records);
    }

    public function testTokenIsHiddenInStackTraces(): void
    {
        $parameter = new \ReflectionParameter([InvoiceApi::class, '__construct'], 'apiToken');

        self::assertCount(1, $parameter->getAttributes(\SensitiveParameter::class));
    }
}
