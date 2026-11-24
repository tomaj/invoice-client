---
description: Configuration reference of invoice-client - every argument of InvoiceApi and GuzzleTransport with its type, default and meaning.
---

# Configuration reference

invoice-client has no configuration file and reads no environment variables. Everything is an argument of `InvoiceApi` or of the transport.

## InvoiceApi

<!-- snippet: skip -->
```php
new InvoiceApi(
    string $apiToken,
    string $target = InvoiceApi::DEFAULT_TARGET,
    ?TransportInterface $transport = null,
    ?LoggerInterface $logger = null,
);
```

| Argument | Type | Default | Meaning |
| --- | --- | --- | --- |
| `apiToken` | `string` | required | the API token of your chcemfakturu.sk account, sent as `Authorization: Bearer <token>`. Marked `#[\SensitiveParameter]`, never logged |
| `target` | `string` | `InvoiceApi::DEFAULT_TARGET` = `https://chcemfakturu.sk` | base URL of the API. Must start with `http://` or `https://`, otherwise `InvalidValueException`. A trailing `/` is removed. Use `http://` only for a local test server |
| `transport` | `?Transport\TransportInterface` | `null`: a new `GuzzleTransport` | how requests are sent, see [Transports](./transports.md) |
| `logger` | `?Psr\Log\LoggerInterface` | `null`: `NullLogger` | receives requests, status codes and API errors, see [Logging](./logging.md) |

`InvoiceApi` is `final readonly`: create it once (for example as a service in your container) and reuse it. It keeps no state between calls.

Named arguments make the intent clear:

```php
$api = new InvoiceApi(
    apiToken: (string) getenv('CHCEMFAKTURU_API_TOKEN'),
    target: 'https://chcemfakturu.sk',
    transport: new GuzzleTransport($guzzle),
    logger: $logger,
);
```

## GuzzleTransport

<!-- snippet: skip -->
```php
new GuzzleTransport(?GuzzleHttp\ClientInterface $client = null);
```

| Argument | Type | Default | Meaning |
| --- | --- | --- | --- |
| `client` | `?GuzzleHttp\ClientInterface` | `null`: `new GuzzleHttp\Client(['timeout' => GuzzleTransport::TIMEOUT])` | your configured Guzzle 7 or 8 client: timeouts, proxy, headers, middleware |

| Constant | Value | Meaning |
| --- | --- | --- |
| `GuzzleTransport::TIMEOUT` | `10` | total timeout of the default client, in seconds |

Whatever client you pass, every request gets the `Authorization` header and `http_errors => false`. Details in [Transports](./transports.md#guzzletransport).

## Other transports

| Class | Constructor | Notes |
| --- | --- | --- |
| `TestTransport` | none | queue answers with `addResponse()`; see [Testing](./testing.md) |
| `NullTransport` | none | sends nothing |

## Example setups

::: code-group

```php [Production]
$api = new InvoiceApi(
    (string) getenv('CHCEMFAKTURU_API_TOKEN'),
    logger: $logger,
);
```

```php [Shorter timeout]
$api = new InvoiceApi(
    (string) getenv('CHCEMFAKTURU_API_TOKEN'),
    transport: new GuzzleTransport(new GuzzleHttp\Client(['connect_timeout' => 2, 'timeout' => 5])),
);
```

```php [Local fake API]
$api = new InvoiceApi('test-token', target: 'http://127.0.0.1:8701');
```

```php [Invoicing switched off]
$api = new InvoiceApi('unused', transport: new NullTransport());
```

:::
