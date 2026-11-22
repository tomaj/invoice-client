<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Tomaj\InvoiceClient\Exception\InvoiceClientException;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;
use Tomaj\InvoiceClient\Transport\HttpMethod;

/**
 * The API server is not reachable from the tests: every request goes to a Guzzle MockHandler.
 */
final class GuzzleTransportTest extends TestCase
{
    private ?MockHandler $handler = null;

    /** @var list<array<mixed>> request options of the requests sent */
    private array $options = [];

    private function transport(Response|\Throwable $response): GuzzleTransport
    {
        $this->handler = new MockHandler([$response]);
        $stack = HandlerStack::create($this->handler);
        $stack->push(Middleware::tap(function (RequestInterface $request, array $options): void {
            $this->options[] = $options;
        }));

        return new GuzzleTransport(new Client(['handler' => $stack]));
    }

    private function lastRequest(): RequestInterface
    {
        $request = $this->handler?->getLastRequest();
        self::assertNotNull($request);

        return $request;
    }

    public function testPostSendsPayloadAndToken(): void
    {
        $transport = $this->transport(new Response(200, [], '{"status":"ok"}'));

        $result = $transport->call(HttpMethod::Post, 'https://example.test/api', 'secret', '{"a":1}');

        self::assertSame(200, $result->getCode());
        self::assertSame('{"status":"ok"}', $result->getBody());
        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://example.test/api', (string) $request->getUri());
        self::assertSame('Bearer secret', $request->getHeaderLine('Authorization'));
        self::assertSame('{"a":1}', (string) $request->getBody());
        self::assertFalse($request->hasHeader('Content-Type'));
    }

    /**
     * @return iterable<string, array{HttpMethod, string}>
     */
    public static function methodsWithoutBody(): iterable
    {
        yield 'GET' => [HttpMethod::Get, 'GET'];
        yield 'DELETE' => [HttpMethod::Delete, 'DELETE'];
    }

    #[DataProvider('methodsWithoutBody')]
    public function testMethodsWithoutBody(HttpMethod $method, string $expected): void
    {
        $transport = $this->transport(new Response(200, [], '{"status":"ok"}'));

        $transport->call($method, 'https://example.test/api/v1/invoices/detail/7', 'secret');

        $request = $this->lastRequest();
        self::assertSame($expected, $request->getMethod());
        self::assertSame('https://example.test/api/v1/invoices/detail/7', (string) $request->getUri());
        self::assertSame('Bearer secret', $request->getHeaderLine('Authorization'));
        self::assertSame('', (string) $request->getBody());
    }

    public function testEmptyPayloadSendsNoBody(): void
    {
        $transport = $this->transport(new Response(200, [], '{}'));

        $transport->call(HttpMethod::Post, 'https://example.test/api', 'secret', '');

        self::assertSame('', (string) $this->lastRequest()->getBody());
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function errorResponses(): iterable
    {
        yield '401' => [401, '{"status":"error","message":"Invalid token"}'];
        yield '404' => [404, '{"status":"error","message":"Invoice not found"}'];
        yield '422' => [422, '{"status":"error","message":"Invalid VAT"}'];
        yield '500' => [500, 'Internal Server Error'];
        yield '503' => [503, '{"status":"error","message":"Down"}'];
    }

    #[DataProvider('errorResponses')]
    public function testErrorResponsesAreReturnedWithoutOutput(int $status, string $body): void
    {
        $transport = $this->transport(new Response($status, [], $body));

        // beStrictAboutOutputDuringTests makes this test fail if the transport echoes anything
        $result = $transport->call(HttpMethod::Post, 'https://example.test/api', 'secret', '{}');

        self::assertSame($status, $result->getCode());
        self::assertSame($body, $result->getBody());
    }

    public function testErrorResponsesAreReturnedAlsoByAClientThatThrowsOnThem(): void
    {
        $transport = $this->transport(new Response(422, [], 'Invalid'));

        $transport->call(HttpMethod::Post, 'https://example.test/api', 'secret', '{}');

        self::assertFalse($this->options[0]['http_errors']);
    }

    public function testConnectionErrorThrowsAPackageException(): void
    {
        $previous = new ConnectException('Connection refused', new Request('POST', 'https://example.test/api'));
        $transport = $this->transport($previous);

        try {
            $transport->call(HttpMethod::Post, 'https://example.test/api', 'secret', '{}');
            self::fail('TransportException expected');
        } catch (InvoiceClientException $e) {
            self::assertInstanceOf(TransportException::class, $e);
            self::assertSame($previous, $e->getPrevious());
            self::assertSame('POST https://example.test/api failed: Connection refused', $e->getMessage());
            self::assertStringNotContainsString('secret', $e->getMessage());
            self::assertSame(0, $e->getCode());
        }
    }

    public function testDefaultClientHasATimeout(): void
    {
        $client = new \ReflectionProperty(GuzzleTransport::class, 'client')->getValue(new GuzzleTransport());
        self::assertInstanceOf(Client::class, $client);

        // Client::getConfig() is deprecated in Guzzle 7 and removed in 8, so read the config property
        $config = new \ReflectionProperty(Client::class, 'config')->getValue($client);
        self::assertIsArray($config);
        self::assertSame(10, $config['timeout']);
    }
}
