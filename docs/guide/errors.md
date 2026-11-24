---
description: Which failures of invoice-client are responses and which are exceptions, every exception class with when it is thrown, and a complete error-handling pattern.
---

# Error handling

Two kinds of failure, handled two ways:

| Kind | Examples | How you see it |
| --- | --- | --- |
| The API answered with an error | wrong token (401), validation error (422), unknown invoice (404), server error (5xx), an HTML error page from a proxy | a response with `isOk() === false` |
| There is no answer, or the input is wrong | DNS failure, connection refused, timeout, TLS error; a non-numeric price, an unsupported currency, invalid UTF-8 | an exception |

## The pattern

<<< @/snippets/error-handling.php

- Catch `TransportException` separately when you want to retry later or alert on an outage.
- `InvoiceClientException` is an interface implemented by every exception of the package, so one `catch` covers the rest.
- Check `isOk()` on every response. Log `getStatusCode()`, `getErrorMessage()` and, for a support ticket, `getBody()`. Do not log the request body: it contains personal data.

## Exceptions

All of them are in `Tomaj\InvoiceClient\Exception\` and implement `InvoiceClientException` (which extends `Throwable`).

| Exception | Extends | Thrown by | When |
| --- | --- | --- | --- |
| `TransportException` | `RuntimeException` | `createInvoice()`, `getInvoice()`, `deleteInvoice()` (from the transport) | no HTTP response: DNS failure, connection refused, timeout, TLS error, too many redirects. The message is `<METHOD> <URL> failed: <reason>`; the Guzzle exception is `getPrevious()` |
| `SerializationException` | `RuntimeException` | `createInvoice()`, `Serializer::encodeInvoice()` | the invoice cannot be encoded as JSON, e.g. a text is not valid UTF-8. The `JsonException` is `getPrevious()`. Nothing is sent |
| `InvalidValueException` | `InvalidArgumentException` | `new InvoiceApi()`; numeric setters; `new Discount()`; `Discount::fromArray()` | a `target` without `http://` or `https://`; a non-numeric string for a number; an unknown discount type |
| `UnsupportedCurrencyException` | `InvalidArgumentException` | `Invoice::fromArray()` | a `currency` that is not a `Currency` case or code |
| `UnsupportedDiscountException` | `InvalidArgumentException` | `Invoice::setDiscount()`, `Invoice::fromArray()` | a flat discount on the whole invoice |

Messages the package writes:

| Exception | Message |
| --- | --- |
| `InvalidValueException` | `Target must start with http:// or https://, 'chcemfakturu.sk' given` |
| `InvalidValueException` | `price_item must be a number or a numeric string, 'forty' given` (the field name of the setter: `price`, `price_total`, `quantity`, `vat`, `price_item`, `value`) |
| `InvalidValueException` | `Unknown discount type 'gift'` |
| `UnsupportedCurrencyException` | `Unsupported currency 'BTC'` |
| `UnsupportedDiscountException` | `Flat discount is unsupported for invoice. Add new item with negative price for flat discount.` |
| `SerializationException` | `Cannot encode the invoice as JSON: Malformed UTF-8 characters, possibly incorrectly encoded` |
| `TransportException` | `POST https://chcemfakturu.sk/api/v1/invoices/create failed: cURL error 28: …` |

PHP itself throws a `TypeError` for a wrong argument type, for example a logger that is not a `Psr\Log\LoggerInterface`, and a `ValueError` from `Currency::from()` for an unknown code.

## Error responses

When the API answers, you get a response even for 4xx and 5xx statuses. [Responses](./responses.md#error-messages) lists every message the client produces when the API's own message is missing or the body cannot be parsed.

```php
$response = $api->createInvoice($invoice);

match (true) {
    $response->isOk() => null,
    $response->getStatusCode() === 401 => throw new RuntimeException('Check the chcemfakturu.sk API token'),
    $response->getStatusCode() >= 500, $response->getStatusCode() === 0 => throw new RuntimeException('Invoicing is down, try again later'),
    default => throw new RuntimeException('Invoice rejected: ' . $response->getErrorMessage()),
};
```

## Retrying

The client never retries. Whether a retry is safe depends on the call:

- `getInvoice()` and `deleteInvoice()` can be retried after a `TransportException` or a 502/503/504.
- `createInvoice()` should **not** be retried blindly: after a timeout the invoice may exist already, and a retry would create a duplicate. Check first (for example with your own `number` or `variable_symbol` in your records), or let a person decide.

The [retries-and-timeouts example](../examples/retries-and-timeouts.md) adds Guzzle retry middleware that retries only `GET` and `DELETE`.

## See every case run

The [error-handling example](../examples/error-handling.md) triggers each response error and each exception above against the fake API and prints what you get.
