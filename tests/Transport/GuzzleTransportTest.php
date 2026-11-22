<?php

declare(strict_types=1);

namespace Invoice\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Invoice\Exception;
use PHPUnit\Framework\TestCase;

class GuzzleTransportTest extends TestCase
{
    private MockHandler $handler;

    private function transport(Response $response): GuzzleTransport
    {
        $this->handler = new MockHandler([$response]);

        return new GuzzleTransport(new Client(['handler' => HandlerStack::create($this->handler)]));
    }

    public function testPostSendsPayloadAndToken(): void
    {
        $transport = $this->transport(new Response(200, [], '{"status":"ok"}'));

        $result = $transport->call('https://example.test/api', 'secret', '{"a":1}');

        self::assertNotNull($result);
        self::assertSame(200, $result->getCode());
        self::assertSame('{"status":"ok"}', $result->getBody());
        $request = $this->handler->getLastRequest();
        self::assertNotNull($request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://example.test/api', (string) $request->getUri());
        self::assertSame('Bearer secret', $request->getHeaderLine('Authorization'));
        self::assertSame('{"a":1}', (string) $request->getBody());
    }

    public function testServerErrorIsReturnedWithoutOutput(): void
    {
        $transport = $this->transport(new Response(503, [], '{"status":"error","message":"Down"}'));

        // beStrictAboutOutputDuringTests makes this test fail if the transport echoes anything
        $result = $transport->call('https://example.test/api', 'secret', '{}');

        self::assertNotNull($result);
        self::assertSame(503, $result->getCode());
        self::assertSame('{"status":"error","message":"Down"}', $result->getBody());
    }

    public function testClientErrorThrows(): void
    {
        $transport = $this->transport(new Response(401, [], 'Unauthorized'));

        $this->expectException(ClientException::class);
        $transport->call('https://example.test/api', 'secret', '{}');
    }

    public function testUnsupportedMethodThrows(): void
    {
        $transport = $this->transport(new Response(200));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("HTTP method 'get' is not supported");
        $transport->call('https://example.test/api', 'secret', null, 'get');
    }
}
