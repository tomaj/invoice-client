---
description: What InvoiceApi logs through a PSR-3 logger - levels, messages and context keys - and what it never logs.
---

# Logging

Pass any [PSR-3](https://www.php-fig.org/psr/psr-3/) logger (Monolog, your framework's logger) as `logger`:

```php
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Tomaj\InvoiceClient\InvoiceApi;

$logger = new Logger('invoices');
$logger->pushHandler(new StreamHandler('php://stderr'));
$logger->pushProcessor(new PsrLogMessageProcessor()); // fills {error} in the message

$api = new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'), logger: $logger);
```

Without a logger the client uses `Psr\Log\NullLogger`.

## What is logged

| When | Level | Message | Context |
| --- | --- | --- | --- |
| before every request | `debug` | `Invoice API request` | `method`, `url` |
| the response is OK | `info` | `Invoice API response` | `method`, `url`, `status` |
| the response is an error | `warning` | `Invoice API error: {error}` | `method`, `url`, `status`, `error` |
| no HTTP response (`TransportException`) | `error` | `Invoice API request failed` | `method`, `url`, `exception` |

- `method` is `GET`, `POST` or `DELETE`; `url` is the full URL including the (URL-encoded) invoice id; `status` is the HTTP status code, `0` when nothing was sent.
- `error` is `getErrorMessage()` of the response. The message uses a PSR-3 placeholder, so a logger with placeholder interpolation shows `Invoice API error: Invalid API token`.
- `exception` is the `TransportException`, logged before it is thrown to your code. Log it there too only if you want it twice.
- An invoice that cannot be encoded (`SerializationException`) is not logged: no request was attempted.

The [logging example](../examples/logging.md) prints every record for a successful call, an API error and a connection failure:

<<< @/../examples/logging/expected-output.txt{text}

## What is never logged

- **The API token.** It is a bearer secret. It is also marked `#[\SensitiveParameter]` in the constructor, so it does not appear in stack traces.
- **The request body and the response body.** Invoices hold names, addresses and VAT numbers of your customers.

Keep it that way in your own code: log `getStatusCode()` and `getErrorMessage()`, and keep `getBody()` for a support ticket rather than a shared log. A custom transport must not log the token or the payload either.

## Debugging a request

To see the exact JSON you send, encode the invoice without sending it:

```php
use Tomaj\InvoiceClient\Serializer;

echo new Serializer()->encodeInvoice($invoice);
```

To see the raw HTTP exchange in development, give `GuzzleTransport` a Guzzle client with `'debug' => true` or a history middleware (see [Transports](./transports.md#your-own-guzzle-client)). Never enable that in production: it prints the `Authorization` header.
