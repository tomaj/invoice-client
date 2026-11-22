# API Reference

Namespace `Tomaj\InvoiceClient`. All classes are `final`; extension happens through `TransportInterface`.

## InvoiceApi

```php
final readonly class InvoiceApi
{
    public const string DEFAULT_TARGET = 'https://chcemfakturu.sk';

    public function __construct(
        string $apiToken,
        string $target = self::DEFAULT_TARGET,
        ?Transport\TransportInterface $transport = null, // GuzzleTransport
        ?Psr\Log\LoggerInterface $logger = null,          // NullLogger
    );

    public function createInvoice(Invoice\Invoice $invoice): Response\CreateInvoiceResponse;
    public function getInvoice(string $id): Response\InvoiceDetailResponse;
    public function deleteInvoice(string $id): Response\DeleteInvoiceResponse;

    public function htmlUrl(string $id): string;
    public function pdfUrl(string $id): string;
    public function pdfDownloadUrl(string $id): string;
}
```

- The constructor throws `Exception\InvalidValueException` when `$target` does not start with `http://` or `https://`; a trailing `/` is removed. `$apiToken` is marked `#[\SensitiveParameter]`.
- The three API calls throw `Exception\TransportException` when no HTTP response arrives; `createInvoice()` also throws `Exception\SerializationException`.

See [API Calls and Responses](../guide/api.md).

## Invoice\\

| Class | Methods |
| --- | --- |
| `Invoice` | `fromArray(array)`, get/set `Id`, `Number`, `Name`, `Created`, `Delivered`, `Due`, `Status`, `Description`, `VariableSymbol`, `ConstantSymbol`, `Price`, `PriceTotal`, `Currency` (`?Currency`), `Client`, `ShippingAddress`, `Issuer`, `Discount`; `getItems()`, `setItems()`, `addItem()` |
| `InvoiceItem` | `fromArray(array)`, get/set `Quantity`, `Vat`, `PriceItem`, `Price`, `PriceTotal`, `Description`, `Discount` |
| `Client` | `fromArray(array)`, get/set `Name`, `Company`, `Identifier`, `VatNumber`, `TaxNumber`, `Address` |
| `Address` | `fromArray(array)`, get/set `Street`, `Street2`, `Zip`, `City`, `Country`, `State`, `Email`, `Tel`, `Fax` |
| `Discount` (readonly) | `__construct(DiscountType $type, int\|float\|string $value)`, `fromArray(array)`, `getType()`, `getValue()` |
| `Issuer` | `fromArray(array)`; has no fields and is not sent |
| `Currency` (enum) | `EUR`, `USD`, `CZK`, `GBP`, `HUF`, `PLN`, `RUB` |
| `DiscountType` (enum) | `None` (`none`), `Percent` (`percent`), `Flat` (`flat`) |

Numeric setters take `int|float|string|null` and throw `Exception\InvalidValueException` for a non-numeric string. Field types and `fromArray()` rules: [Building Invoices](../guide/invoices.md).

## Serializer

`encodeInvoice(Invoice $invoice): string` returns the JSON request body of `createInvoice()`; throws `Exception\SerializationException`.

## Response\\

```php
interface ResponseInterface
{
    public function isOk(): bool;
    public function getStatusCode(): int;
    public function getErrorMessage(): ?string;
    public function getBody(): string;
    public function getData(): array;
}
```

| Class | Additional methods |
| --- | --- |
| `CreateInvoiceResponse` | `getId(): ?string`, `getHtmlUrl(): ?string`, `getDownloadUrl(): ?string` |
| `InvoiceDetailResponse` | none, the invoice is in `getData()` |
| `DeleteInvoiceResponse` | none |

Every response class takes a `?Transport\Result` in its constructor.

## Transport\\

```php
interface TransportInterface
{
    /** @throws Exception\TransportException when there is no HTTP response */
    public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result;
}
```

| Class | Description |
| --- | --- |
| `GuzzleTransport` | `__construct(?GuzzleHttp\ClientInterface $client = null)`; `TIMEOUT = 10` seconds for the default client |
| `NullTransport` | returns `null` |
| `TestTransport` | `addResponse(Result): self`, `getRequests(): list<array{method, url, apiToken, payload}>`, `getLastRequest(): ?array` |
| `Result` (readonly) | `__construct(int $code, string $body)`, `getCode()`, `getBody()` |
| `HttpMethod` (enum) | `Get`, `Post`, `Delete` |

## Exception\\

| Class | Extends |
| --- | --- |
| `InvoiceClientException` | interface, `Throwable`; implemented by all below |
| `TransportException` | `RuntimeException` |
| `SerializationException` | `RuntimeException` |
| `InvalidValueException` | `InvalidArgumentException` |
| `UnsupportedCurrencyException` | `InvalidArgumentException` |
| `UnsupportedDiscountException` | `InvalidArgumentException` |
