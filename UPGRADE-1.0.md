# Upgrading from dev-master to 1.0

invoice-client was never tagged before 1.0.0, so this file is for projects that installed `dev-master` (the 2018 code on Packagist, or master during 2026). Every change is in [CHANGELOG.md](CHANGELOG.md).

Checklist:

- PHP 8.4, Guzzle 7.15.2+ or 8, `psr/log` 2 or 3: [Requirements](#requirements).
- New namespace `Tomaj\InvoiceClient\`: [Namespace](#namespace).
- `InvoiceApi` takes named arguments instead of an options array: [Constructor](#invoiceapi-constructor).
- API errors are responses, not exceptions; `getErrorMessage()` returns `?string`: [Responses](#responses-and-errors).
- `Currency` and `DiscountType` are enums: [Enums](#currency-and-discount-enums).
- Custom transports and `TestTransport`: [Transports](#transports).
- Exceptions: [Exceptions](#exceptions).

## Requirements

- PHP 8.4 or newer (the 2018 `dev-master` ran on PHP 5.5+).
- `guzzlehttp/guzzle` `^7.15.2 || ^8.0.1` (was `^6.0`).
- `psr/log` `^2 || ^3` is now a dependency.

## Namespace

Classes moved from `Invoice\` to `Tomaj\InvoiceClient\` and are autoloaded with PSR-4:

| Before | After |
| --- | --- |
| `Invoice\InvoiceApi` | `Tomaj\InvoiceClient\InvoiceApi` |
| `Invoice\Serializer` | `Tomaj\InvoiceClient\Serializer` |
| `Invoice\Invoice`, `InvoiceItem`, `Client`, `Address`, `Discount`, `Issuer`, `Currency` | `Tomaj\InvoiceClient\Invoice\…` |
| `Invoice\Transport\…` | `Tomaj\InvoiceClient\Transport\…` |
| `Invoice\Response\…` | `Tomaj\InvoiceClient\Response\…` |
| `Invoice\Exception` | `Tomaj\InvoiceClient\Exception\InvoiceClientException` (interface) |
| `Invoice\Exception\UnsupportedCurrencyException` | `Tomaj\InvoiceClient\Exception\UnsupportedCurrencyException` |

```php
// before
use Invoice\InvoiceApi;
use Invoice\Invoice;
use Invoice\InvoiceItem;

// after
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
```

## InvoiceApi constructor

```php
// before
$api = new InvoiceApi($token, [
    'target' => 'https://chcemfakturu.sk/',
    'transport' => new GuzzleTransport(),
    'logger' => $logger,
    'debug' => true,
]);

// after
$api = new InvoiceApi(
    $token,
    target: 'https://chcemfakturu.sk',
    transport: new GuzzleTransport(),
    logger: $logger,
);
```

- `debug` is gone: invalid arguments always throw (`InvalidValueException` for a target without `http://`/`https://`, a `TypeError` for a logger that is not a `Psr\Log\LoggerInterface`). Before, an invalid logger without `debug` silently left the client without a transport.
- `getLogger()` is removed. The logger is now actually used: requests, status codes and API errors are logged (never the token or invoice data).

## Responses and errors

Before, a 4xx response threw Guzzle's `ClientException` and `getInvoice()`/`deleteInvoice()` threw `Invoice\Exception` with the default transport. Now every call returns a response object:

```php
// before
try {
    $result = $api->createInvoice($invoice);
    if (!$result->isOk()) {
        echo $result->getErrorMessage(); // string|false
    }
} catch (\GuzzleHttp\Exception\ClientException $e) {
    // 401, 404, 422, ...
}

// after
$response = $api->createInvoice($invoice);
if (!$response->isOk()) {
    echo $response->getStatusCode(), ' ', $response->getErrorMessage(); // ?string, null when OK
    echo $response->getBody();
}
```

- `getErrorMessage()` returns `null` instead of `false` when the request succeeded. Replace `=== false` checks with `isOk()` or `=== null`.
- `isOk()` additionally requires a 2xx HTTP status.
- A body that is not JSON reports `Cannot parse response from server (HTTP <status>)` (was without the status).
- `getInvoice()` returns `InvoiceDetailResponse` (the invoice is in `getData()`) and `deleteInvoice()` returns `DeleteInvoiceResponse`, instead of a raw `Transport\Result`.
- Only a request without any HTTP response throws, as `TransportException`.

## Currency and discount enums

```php
// before
$invoice->setCurrency(Currency::EUR); // the string 'EUR'
$invoice->setCurrency('EUR');
$discount = new Discount('percent', 10);
$discount->setValue(15);

// after
$invoice->setCurrency(Currency::EUR); // the enum case
$invoice->setCurrency(Currency::from('EUR'));
$discount = new Discount(DiscountType::Percent, 10);
$discount = new Discount(DiscountType::Percent, 15); // Discount is immutable
```

- `Invoice::getCurrency()` returns `?Currency`; use `->value` for the code. `Currency::isValid($code)` is replaced by `Currency::tryFrom($code) !== null`.
- `Invoice::fromArray()` still takes the currency code (`'currency' => 'EUR'`) and throws `UnsupportedCurrencyException` for an unknown one.
- `Discount::getType()` returns `DiscountType`; `Discount::fromArray()` throws `InvalidValueException` for a type other than `none`, `percent` or `flat`.

## Transports

`TransportInterface::call()` takes an `HttpMethod` enum first:

<!-- snippet: skip -->
```php
// before
public function call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result;

// after
public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result;
```

Return every HTTP response as a `Result` (4xx and 5xx included) and throw `TransportException` only when there is no response.

`TestTransport` records all requests and queues responses:

```php
// before
$transport->setResponse(new Result(200, $body));
$api->createInvoice($invoice);
$payload = $transport->getPayload();
$method = $transport->getMethod(); // 'post'

// after
$transport->addResponse(new Result(200, $body));
$api->createInvoice($invoice);
$request = $transport->getLastRequest();
$payload = $request['payload'];
$method = $request['method']; // HttpMethod::Post
```

## Exceptions

All exceptions implement `Tomaj\InvoiceClient\Exception\InvoiceClientException`:

| Before | After |
| --- | --- |
| `Invoice\Exception` (flat invoice discount) | `UnsupportedDiscountException` |
| `\InvalidArgumentException` (non-numeric price) | `InvalidValueException` (still an `InvalidArgumentException`) |
| `\JsonException` from `Serializer::encodeInvoice()` | `SerializationException`, the `JsonException` is `getPrevious()` |
| Guzzle exceptions | `TransportException` for missing responses; HTTP errors are responses |

## Other removals

- All classes are `final`; `Result` and `Discount` are readonly.
- `Serializer::decodeInvoice()` is removed; `encodeClient()`, `encodeAddress()` and `encodeItems()` are private. Use `encodeInvoice()`.
