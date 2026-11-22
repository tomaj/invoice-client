# API Calls and Responses

```php
$api = new InvoiceApi(
    apiToken: $token,
    target: InvoiceApi::DEFAULT_TARGET, // https://chcemfakturu.sk
    transport: null,                    // GuzzleTransport with a 10 second timeout
    logger: null,                       // any PSR-3 logger
);
```

`target` must start with `http://` or `https://`, otherwise `InvalidValueException` is thrown. Use `https://` everywhere except a local test server: the token travels in every request.

## Calls

| Method | Request | Returns |
| --- | --- | --- |
| `createInvoice(Invoice $invoice)` | `POST /api/v1/invoices/create` with the invoice as JSON | `CreateInvoiceResponse` |
| `getInvoice(string $id)` | `GET /api/v1/invoices/detail/{id}` | `InvoiceDetailResponse` |
| `deleteInvoice(string $id)` | `DELETE /api/v1/invoices/delete/{id}` | `DeleteInvoiceResponse` |

Every request carries `Authorization: Bearer <token>`. The id is URL-encoded.

URLs of an invoice, without a request:

| Method | URL |
| --- | --- |
| `htmlUrl(string $id)` | `{target}/html/{id}` |
| `pdfUrl(string $id)` | `{target}/pdf/{id}` |
| `pdfDownloadUrl(string $id)` | `{target}/pdf-download/{id}` |

## Responses

Every call returns a response, whatever the API answered. All responses implement `ResponseInterface`:

| Method | Meaning |
| --- | --- |
| `isOk(): bool` | `true` when the HTTP status is 2xx **and** the JSON body has `"status": "ok"` |
| `getStatusCode(): int` | the HTTP status, `0` when nothing was sent (`NullTransport`) |
| `getErrorMessage(): ?string` | `null` when OK; otherwise the API's `message`, or a description below |
| `getBody(): string` | the raw response body, also for errors |
| `getData(): array` | the decoded JSON object, `[]` when the body is not a JSON object |

Error messages the client creates itself:

| Situation | `getErrorMessage()` |
| --- | --- |
| The body is not a JSON object (HTML error page, empty body) | `Cannot parse response from server (HTTP 502)` |
| `"status"` is not `"ok"` and there is no `message` | `Unknown error` |
| `"status": "ok"` but the HTTP status is not 2xx | `Unexpected HTTP status 500` |
| Nothing was sent (`NullTransport`) | `No request was sent` |

`CreateInvoiceResponse` adds `getId()`, `getHtmlUrl()` and `getDownloadUrl()` (all `null` unless OK). `InvoiceDetailResponse` returns the invoice through `getData()`, exactly as the API sends it. `DeleteInvoiceResponse` has only the common methods.

::: warning Detail and delete responses
The response format of the detail and delete endpoints is assumed to follow the create endpoint (`"status": "ok"` or `"status": "error"` with a `message`). If your account's API answers differently, check `getStatusCode()` and `getBody()`, and please [open an issue](https://github.com/tomaj/invoice-client/issues).
:::

## Exceptions

Exceptions mean the request could not be made. All of them implement `Tomaj\InvoiceClient\Exception\InvoiceClientException`:

| Exception | When |
| --- | --- |
| `TransportException` (`RuntimeException`) | no HTTP response: DNS, connection refused, timeout, TLS error, too many redirects; the Guzzle exception is `getPrevious()` |
| `SerializationException` (`RuntimeException`) | `createInvoice()` cannot encode the invoice as JSON, e.g. a text is not valid UTF-8 |
| `InvalidValueException` (`InvalidArgumentException`) | a non-numeric price, an unknown discount type, a target without `http(s)://` |
| `UnsupportedCurrencyException` (`InvalidArgumentException`) | `Invoice::fromArray()` with an unknown currency |
| `UnsupportedDiscountException` (`InvalidArgumentException`) | a flat discount on the whole invoice |

<<< @/snippets/error-handling.php

## Logging

Pass a PSR-3 logger (Monolog or any other) as `logger`. The client logs:

| Level | Message | Context |
| --- | --- | --- |
| `debug` | `Invoice API request` | `method`, `url` |
| `info` | `Invoice API response` | `method`, `url`, `status` |
| `warning` | `Invoice API error: {error}` | `method`, `url`, `status`, `error` |
| `error` | `Invoice API request failed` | `method`, `url`, `exception` (a `TransportException`) |

The API token, the request body and the response body are never logged, because they contain the secret and personal data.
