---
description: The response objects of invoice-client - when isOk() is true, every error message, getData(), and the extra getters of CreateInvoiceResponse.
---

# Responses

Every call of `InvoiceApi` returns a response object, whatever the API answered. All of them implement `Tomaj\InvoiceClient\Response\ResponseInterface`:

| Method | Returns |
| --- | --- |
| `isOk(): bool` | `true` when the HTTP status is 2xx **and** the JSON body has `"status": "ok"` |
| `getStatusCode(): int` | the HTTP status code; `0` when no request was sent (`NullTransport`, or `TestTransport` without a queued response) |
| `getErrorMessage(): ?string` | `null` when OK; otherwise the API's `message`, or one of the messages below |
| `getBody(): string` | the raw response body, also for errors; `''` when no request was sent |
| `getData(): array` | the decoded JSON object; `[]` when the body is not a JSON object |

## When is a response OK?

Both conditions must hold:

1. the HTTP status is between 200 and 299, and
2. the body is a JSON object with `"status": "ok"`.

A 200 with `{"status": "error", ...}` is an error, and so is a 500 with `{"status": "ok"}`.

## Error messages

| Situation | `getErrorMessage()` |
| --- | --- |
| The JSON has a non-empty string `message` and the response is not OK | the `message`, e.g. `Invalid API token` |
| The body is not a JSON object: an HTML error page, an empty body, a JSON list, or `{}` | `Cannot parse response from server (HTTP 502)` |
| `"status"` is not `"ok"` and there is no usable `message` | `Unknown error` |
| `"status": "ok"` but the HTTP status is not 2xx, without a `message` | `Unexpected HTTP status 500` |
| No request was sent | `No request was sent` |

The [error-handling example](../examples/error-handling.md) produces each of them against the fake API.

## getData()

`getData()` is the decoded body when it is a JSON object (an associative array), and `[]` otherwise. It is filled for errors too, so you can read extra fields an error response may carry:

```php
$response = $api->getInvoice('inv-1');
$data = $response->getData(); // e.g. ['status' => 'ok', 'invoice' => [...]] with the fake API
```

The client does not map the detail into objects: `getData()` is exactly what the API sent.

## CreateInvoiceResponse

Returned by `createInvoice()`. Adds three getters, all `null` unless the response is OK:

| Method | JSON field | Meaning |
| --- | --- | --- |
| `getId(): ?string` | `id` | id of the new invoice; an integer id is returned as a string |
| `getHtmlUrl(): ?string` | `html_url` | link to the HTML version |
| `getDownloadUrl(): ?string` | `download_pdf_url` | link to download the PDF |

A successful answer looks like this:

```json
{
    "status": "ok",
    "id": "inv-0001",
    "html_url": "https://chcemfakturu.sk/html/inv-0001",
    "download_pdf_url": "https://chcemfakturu.sk/pdf-download/inv-0001"
}
```

A field that is missing, or not a string or integer, gives `null`.

## InvoiceDetailResponse

Returned by `getInvoice()`. Has only the common methods; the invoice is in `getData()`, as the API sent it.

## DeleteInvoiceResponse

Returned by `deleteInvoice()`. Has only the common methods; `isOk()` tells whether the invoice was deleted.

::: warning Assumed format
The format of the detail and delete answers is not verified against the live API: the client assumes the same envelope as create (`"status"` and `"message"`). If it differs for your account, `getStatusCode()`, `getBody()` and `getData()` still hold the raw answer. See [API calls](./api.md#delete-an-invoice).
:::

## Building a response yourself

Every response class takes a `?Transport\Result` (status code and body) in its constructor, which is handy in tests of code that receives a response:

```php
use Tomaj\InvoiceClient\Response\CreateInvoiceResponse;
use Tomaj\InvoiceClient\Transport\Result;

$response = new CreateInvoiceResponse(new Result(422, '{"status":"error","message":"Invalid VAT number"}'));
$response->isOk();            // false
$response->getErrorMessage(); // 'Invalid VAT number'
```
