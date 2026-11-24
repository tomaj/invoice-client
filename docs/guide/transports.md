---
description: The transports of invoice-client - GuzzleTransport with your own Guzzle client, NullTransport, TestTransport - and how to write your own, e.g. on PSR-18.
---

# Transports

A transport sends one HTTP request and returns its status code and body as a `Transport\Result`. `InvoiceApi` uses `GuzzleTransport` unless you pass another one as `transport`.

| Transport | Sends | Use |
| --- | --- | --- |
| `GuzzleTransport` | real HTTP requests with Guzzle 7 or 8 | production, the default |
| `TestTransport` | nothing; returns the responses you queue and records the requests | your tests, see [Testing](./testing.md) |
| `NullTransport` | nothing; every call returns a response with status `0` and `No request was sent` | switching invoicing off, e.g. in a staging environment |

## GuzzleTransport

```php
use Tomaj\InvoiceClient\Transport\GuzzleTransport;

new GuzzleTransport();                 // a new Guzzle client with a 10 second timeout
new GuzzleTransport($yourGuzzleClient); // any GuzzleHttp\ClientInterface
```

For every request it:

- sets `Authorization: Bearer <token>` (merged with your client's default headers),
- sends the JSON as the body when there is one (`createInvoice()`); `GET` and `DELETE` have no body,
- sets the request option `http_errors` to `false`, so a 4xx or 5xx answer is returned as a `Result` instead of a Guzzle exception, whatever your client's own setting,
- turns every `GuzzleHttp\Exception\GuzzleException` (no response) into a `TransportException` with the Guzzle exception as `getPrevious()`.

`GuzzleTransport::TIMEOUT` (`10`) is the `timeout` of the default client in seconds. Your own client keeps the timeouts you give it.

### Your own Guzzle client

Pass a configured client for other timeouts, a proxy, extra headers or middleware:

<<< @/snippets/transport-options.php

Useful [Guzzle request options](https://docs.guzzlephp.org/en/stable/request-options.html):

| Option | Why |
| --- | --- |
| `timeout`, `connect_timeout` | how long to wait for the API; a timeout becomes a `TransportException` |
| `headers` | e.g. a `User-Agent` identifying your application, or `Content-Type: application/json` if your API account requires it (the client does not set one) |
| `proxy` | an outgoing proxy |
| `handler` | a `HandlerStack` with middleware: retries, history for debugging, a `MockHandler` in tests |

The [retries-and-timeouts example](../examples/retries-and-timeouts.md) builds a client with a retry middleware, a timeout and an extra header, and runs it against a failing and a slow fake API.

::: danger Keep TLS verification on
Do not set `verify` to `false` on a client that carries your API token: anyone on the network path could read it.
:::

## NullTransport

```php
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\NullTransport;

$api = new InvoiceApi('unused', transport: new NullTransport());
$api->createInvoice($invoice)->getErrorMessage(); // 'No request was sent'
```

`call()` returns `null`; every response then reports status `0` and `No request was sent`, and `isOk()` is `false`, so your code takes its error path without a network call.

## TestTransport

Records every request and returns the responses you queue, in order. Described with examples in [Testing your integration](./testing.md#testtransport).

## Writing a transport

Implement `Transport\TransportInterface`:

<!-- snippet: skip -->
```php
public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result;
```

| Argument | Meaning |
| --- | --- |
| `$method` | `HttpMethod::Get`, `Post` or `Delete`; `->value` is the HTTP method string |
| `$url` | the full URL, id already URL-encoded |
| `$apiToken` | send it as `Authorization: Bearer <token>`; never log it |
| `$payload` | the JSON body, or `null` for none |

Return every HTTP response as a `new Result($statusCode, $body)`, 4xx and 5xx included. Throw `TransportException` only when there is no response. Return `null` only if your transport deliberately sends nothing.

A transport on any PSR-18 HTTP client:

```php
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Transport\HttpMethod;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TransportInterface;

final readonly class Psr18Transport implements TransportInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requests,
        private StreamFactoryInterface $streams,
    ) {}

    public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result
    {
        $request = $this->requests->createRequest($method->value, $url)
            ->withHeader('Authorization', 'Bearer ' . $apiToken);
        if ($payload !== null && $payload !== '') {
            $request = $request->withBody($this->streams->createStream($payload));
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new TransportException(sprintf('%s %s failed: %s', $method->value, $url, $e->getMessage()), 0, $e);
        }

        return new Result($response->getStatusCode(), (string) $response->getBody());
    }
}
```
