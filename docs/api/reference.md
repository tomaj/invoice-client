---
description: API reference of tomaj/invoice-client - every public class, method, constant, enum case and exception, by namespace.
---

# API reference

Root namespace `Tomaj\InvoiceClient`. All classes are `final`; extend the client by implementing `Transport\TransportInterface`. Every file declares `strict_types=1`.

| Namespace | Contents |
| --- | --- |
| [`Tomaj\InvoiceClient`](#invoiceapi) | `InvoiceApi`, `Serializer` |
| [`…\Invoice`](#invoice) | `Invoice`, `InvoiceItem`, `Client`, `Address`, `Discount`, `Issuer`, `Currency`, `DiscountType` |
| [`…\Response`](#response) | `ResponseInterface`, `CreateInvoiceResponse`, `InvoiceDetailResponse`, `DeleteInvoiceResponse` |
| [`…\Transport`](#transport) | `TransportInterface`, `GuzzleTransport`, `TestTransport`, `NullTransport`, `Result`, `HttpMethod` |
| [`…\Exception`](#exception) | `InvoiceClientException` and five exceptions |

## InvoiceApi

`final readonly class Tomaj\InvoiceClient\InvoiceApi`: the client.

| Member | Description |
| --- | --- |
| `const string DEFAULT_TARGET = 'https://chcemfakturu.sk'` | default base URL |
| `__construct(string $apiToken, string $target = self::DEFAULT_TARGET, ?TransportInterface $transport = null, ?LoggerInterface $logger = null)` | throws `InvalidValueException` when `$target` does not start with `http://` or `https://`; removes a trailing `/`; defaults to `GuzzleTransport` and `NullLogger`. `$apiToken` is `#[\SensitiveParameter]` |
| `createInvoice(Invoice $invoice): CreateInvoiceResponse` | `POST {target}/api/v1/invoices/create`; throws `SerializationException`, `TransportException` |
| `getInvoice(string $id): InvoiceDetailResponse` | `GET {target}/api/v1/invoices/detail/{id}`; throws `TransportException` |
| `deleteInvoice(string $id): DeleteInvoiceResponse` | `DELETE {target}/api/v1/invoices/delete/{id}`; throws `TransportException` |
| `htmlUrl(string $id): string` | `{target}/html/{id}`, no request |
| `pdfUrl(string $id): string` | `{target}/pdf/{id}`, no request |
| `pdfDownloadUrl(string $id): string` | `{target}/pdf-download/{id}`, no request |

Ids are URL-encoded with `rawurlencode()`. Guide: [API calls](../guide/api.md), [Configuration](../guide/configuration.md).

## Serializer

`final class Tomaj\InvoiceClient\Serializer`

| Method | Description |
| --- | --- |
| `encodeInvoice(Invoice $invoice): string` | the JSON request body `{"invoice": {...}}` of `createInvoice()`; throws `SerializationException` (with the `JsonException` as previous) |

## Invoice

Namespace `Tomaj\InvoiceClient\Invoice`. Field meanings and JSON names: [Building invoices](../guide/invoices.md).

### Invoice

`final class Invoice`. Every setter returns `Invoice`.

| Getter | Setter | Type |
| --- | --- | --- |
| `getId()` | `setId()` | `string\|int\|null` |
| `getNumber()` | `setNumber()` | `string\|int\|null` |
| `getName()` | `setName()` | `?string` |
| `getCreated()` | `setCreated()` | `?string` |
| `getDelivered()` | `setDelivered()` | `?string` |
| `getDue()` | `setDue()` | `?string` |
| `getStatus()` | `setStatus()` | `?string` |
| `getDescription()` | `setDescription()` | `?string` |
| `getVariableSymbol()` | `setVariableSymbol()` | `string\|int\|null` |
| `getConstantSymbol()` | `setConstantSymbol()` | `string\|int\|null` |
| `getPrice()` | `setPrice()` | get `int\|float\|numeric-string\|null`, set `int\|float\|string\|null` (throws `InvalidValueException` for a non-numeric string) |
| `getPriceTotal()` | `setPriceTotal()` | as `price` |
| `getCurrency()` | `setCurrency()` | `?Currency` |
| `getClient()` | `setClient(Client $client)` | get `?Client` |
| `getShippingAddress()` | `setShippingAddress(?Address $address = null)` | `?Address` |
| `getIssuer()` | `setIssuer(?Issuer $issuer = null)` | `?Issuer` |
| `getDiscount()` | `setDiscount(Discount $discount)` | get `?Discount`; set throws `UnsupportedDiscountException` for `DiscountType::Flat` |
| `getItems(): list<InvoiceItem>` | `setItems(list<InvoiceItem> $items)`, `addItem(InvoiceItem $item)` | |

`static fromArray(array $data): Invoice` throws `UnsupportedCurrencyException`, `InvalidValueException` (discount type), `UnsupportedDiscountException` (flat invoice discount).

### InvoiceItem

`final class InvoiceItem`. Every setter returns `InvoiceItem`; numeric setters take `int|float|string|null` and throw `InvalidValueException` for a non-numeric string.

| Getter | Setter | Type |
| --- | --- | --- |
| `getQuantity()` | `setQuantity()` | numeric |
| `getVat()` | `setVat()` | numeric |
| `getPriceItem()` | `setPriceItem()` | numeric |
| `getPrice()` | `setPrice()` | numeric |
| `getPriceTotal()` | `setPriceTotal()` | numeric |
| `getDescription()` | `setDescription()` | `?string` |
| `getDiscount()` | `setDiscount(?Discount $discount)` | `?Discount` |

`static fromArray(array $data): InvoiceItem` throws `InvalidValueException` for an unknown discount type.

### Client

`final class Client`. Every setter returns `Client`.

| Getter | Setter | Type |
| --- | --- | --- |
| `getName()` | `setName()` | `?string` |
| `getCompany()` | `setCompany()` | `?string` |
| `getIdentifier()` | `setIdentifier()` | `string\|int\|null` |
| `getVatNumber()` | `setVatNumber()` | `string\|int\|null` |
| `getTaxNumber()` | `setTaxNumber()` | `string\|int\|null` |
| `getAddress()` | `setAddress(?Address $address = null)` | `?Address` |

`static fromArray(array $data): Client`

### Address

`final class Address`. Every setter returns `Address`.

| Getter | Setter | Type |
| --- | --- | --- |
| `getStreet()` | `setStreet()` | `?string` |
| `getStreet2()` | `setStreet2()` | `?string` |
| `getZip()` | `setZip()` | `string\|int\|null` |
| `getCity()` | `setCity()` | `?string` |
| `getCountry()` | `setCountry()` | `?string` |
| `getState()` | `setState()` | `?string` |
| `getEmail()` | `setEmail()` | `?string` |
| `getTel()` | `setTel()` | `string\|int\|null` |
| `getFax()` | `setFax()` | `string\|int\|null` |

`static fromArray(array $data): Address`

### Discount

`final readonly class Discount`: an immutable value object.

| Member | Description |
| --- | --- |
| `__construct(DiscountType $type, int\|float\|string $value)` | throws `InvalidValueException` for a non-numeric string value |
| `static fromArray(array $data): Discount` | reads `type` (default `none`; throws `InvalidValueException` for an unknown one) and `value` (converted to `float`, default `0`) |
| `getType(): DiscountType` | |
| `getValue(): int\|float\|numeric-string` | as given |

### Issuer

`final class Issuer`: has no fields and is not sent; the issuer is the account of the API token. `static fromArray(array $data): Issuer` ignores `$data`.

### Currency

`enum Currency: string`: `EUR`, `USD`, `CZK`, `GBP`, `HUF`, `PLN`, `RUB` (each case's value is its code). Use `Currency::from()` / `tryFrom()` for a string code.

### DiscountType

`enum DiscountType: string`

| Case | Value | Meaning |
| --- | --- | --- |
| `None` | `none` | no discount; not sent |
| `Percent` | `percent` | the value is a percentage |
| `Flat` | `flat` | the value is an amount; items only |

## Response

Namespace `Tomaj\InvoiceClient\Response`. Guide: [Responses](../guide/responses.md).

### ResponseInterface

| Method | Description |
| --- | --- |
| `isOk(): bool` | HTTP 2xx and `"status": "ok"` |
| `getStatusCode(): int` | HTTP status, `0` when no request was sent |
| `getErrorMessage(): ?string` | `null` when OK |
| `getBody(): string` | raw body |
| `getData(): array` | decoded JSON object, `[]` when the body is not one |

### CreateInvoiceResponse

`final class CreateInvoiceResponse implements ResponseInterface`

| Method | Description |
| --- | --- |
| `__construct(?Result $result)` | parses the result; `null` means no request was sent |
| `getId(): ?string` | `id` of the new invoice, `null` unless OK |
| `getHtmlUrl(): ?string` | `html_url`, `null` unless OK |
| `getDownloadUrl(): ?string` | `download_pdf_url`, `null` unless OK |

### InvoiceDetailResponse

`final class InvoiceDetailResponse implements ResponseInterface`: `__construct(?Result $result)`; the invoice is in `getData()`.

### DeleteInvoiceResponse

`final class DeleteInvoiceResponse implements ResponseInterface`: `__construct(?Result $result)`; `isOk()` tells whether the invoice was deleted.

## Transport

Namespace `Tomaj\InvoiceClient\Transport`. Guide: [Transports](../guide/transports.md), [Testing](../guide/testing.md).

### TransportInterface

| Method | Description |
| --- | --- |
| `call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result` | sends one request with `Authorization: Bearer <token>` and `$payload` as the body; returns every HTTP response (4xx and 5xx included), `null` when nothing is sent; throws `TransportException` when there is no response |

### GuzzleTransport

`final readonly class GuzzleTransport implements TransportInterface`

| Member | Description |
| --- | --- |
| `const int TIMEOUT = 10` | timeout of the default client, seconds |
| `__construct(?GuzzleHttp\ClientInterface $client = null)` | your client, or a new one with `timeout` = `TIMEOUT` |
| `call(…): Result` | never returns `null` |

### TestTransport

`final class TestTransport implements TransportInterface`

| Method | Description |
| --- | --- |
| `addResponse(Result $response): self` | queues the answer of the next call |
| `getRequests(): list<array{method: HttpMethod, url: string, apiToken: string, payload: ?string}>` | all recorded requests |
| `getLastRequest(): ?array` | the last request or `null` |
| `call(…): ?Result` | records the request, returns the next queued answer or `null` |

### NullTransport

`final class NullTransport implements TransportInterface`: `call(…)` returns `null`.

### Result

`final readonly class Result`

| Method | Description |
| --- | --- |
| `__construct(int $code, string $body)` | |
| `getCode(): int` | HTTP status code |
| `getBody(): string` | response body |

### HttpMethod

`enum HttpMethod: string`: `Get` (`GET`), `Post` (`POST`), `Delete` (`DELETE`).

## Exception

Namespace `Tomaj\InvoiceClient\Exception`. Guide: [Error handling](../guide/errors.md).

| Type | Extends | Thrown when |
| --- | --- | --- |
| `interface InvoiceClientException` | `Throwable` | implemented by every exception below |
| `TransportException` | `RuntimeException` | no HTTP response |
| `SerializationException` | `RuntimeException` | the invoice cannot be encoded as JSON |
| `InvalidValueException` | `InvalidArgumentException` | invalid target, non-numeric number, unknown discount type |
| `UnsupportedCurrencyException` | `InvalidArgumentException` | unknown currency in `Invoice::fromArray()` |
| `UnsupportedDiscountException` | `InvalidArgumentException` | flat discount on the whole invoice |

## Internal

`Invoice\ArrayReader` and `Response\ResponseTrait` are traits marked `@internal`: they implement `fromArray()` and the response parsing and are not part of the public API.
