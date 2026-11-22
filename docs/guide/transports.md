# Transports and Testing

A transport sends one request and returns the HTTP status and body as a `Result`. `InvoiceApi` uses `GuzzleTransport` unless you pass another one.

| Transport | Use |
| --- | --- |
| `GuzzleTransport` | the default: sends the requests with Guzzle |
| `NullTransport` | sends nothing; every call returns a response with status `0` and `isOk() === false` |
| `TestTransport` | for your tests: records the requests and returns the responses you queue |

## GuzzleTransport

Without arguments it creates a Guzzle client with a 10 second timeout (`GuzzleTransport::TIMEOUT`). Pass your own client for other timeouts, a proxy, middleware or retries:

<<< @/snippets/transport-options.php

Every HTTP response is returned, 4xx and 5xx included (the transport sets `http_errors` to `false` on each request, so your client may keep its own setting). Only a request without a response throws `TransportException`.

Keep TLS verification on: do not set `verify` to `false` on a client that carries your API token.

## Testing your code

`TestTransport` needs no network and no API token:

<<< @/snippets/test-transport.php

Output:

<<< @/snippets/test-transport.php.out{text}

- `addResponse(Result)` queues the next response; without a queued response a call returns `null` and the response reports `No request was sent`.
- `getRequests()` lists every request as `['method' => HttpMethod, 'url' => string, 'apiToken' => string, 'payload' => ?string]`; `getLastRequest()` returns the last one or `null`.

To test the HTTP layer itself, pass a Guzzle client with a [`MockHandler`](https://docs.guzzlephp.org/en/stable/testing.html) to `GuzzleTransport`, as this package's own tests do.

## Writing a transport

Implement `TransportInterface`:

```php
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Transport\HttpMethod;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TransportInterface;

final class Psr18Transport implements TransportInterface
{
    public function __construct(
        private readonly Psr\Http\Client\ClientInterface $client,
        private readonly Psr\Http\Message\RequestFactoryInterface $requests,
        private readonly Psr\Http\Message\StreamFactoryInterface $streams,
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
        } catch (Psr\Http\Client\ClientExceptionInterface $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }

        return new Result($response->getStatusCode(), (string) $response->getBody());
    }
}
```

Return every HTTP response as a `Result`, throw `TransportException` only when there is none, and never log the token or the payload.
