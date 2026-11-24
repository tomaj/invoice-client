---
description: Trigger every error response and every exception of invoice-client against the fake API and see what each one looks like.
---

# Example: error handling

<ProjectSource name="error-handling" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

Every way a call can fail, triggered on purpose. The first half gets an answer from the API (or nothing at all from `NullTransport`) and returns a response with `isOk() === false`; the second half throws, and a single `catch (InvoiceClientException $e)` handles all of them.
## The code

<<< @/../examples/error-handling/run.php

## Output

<<< @/../examples/error-handling/expected-output.txt{text}

## What to notice

| Case | Kind | Why |
| --- | --- | --- |
| wrong token | response, 401 | the API answered; its `message` is the error |
| invoice without items | response, 422 | a validation error of the API (the fake API rejects an invoice without items) |
| unknown invoice | response, 404 | the API answered |
| HTML error page from a proxy | response, 502 | the body is not JSON: `Cannot parse response from server (HTTP 502)`, the HTML is still in `getBody()` |
| `NullTransport` | response, status 0 | nothing was sent: `No request was sent` |
| server not reachable | `TransportException` | no HTTP response; the message ends with the cURL error, which differs per system, so the example prints only its start |
| target without a scheme | `InvalidValueException` | thrown by the constructor |
| non-numeric price | `InvalidValueException` | thrown by the setter, before anything is sent |
| flat discount on the whole invoice | `UnsupportedDiscountException` | the API supports flat discounts on items only |
| unknown discount type | `InvalidValueException` | `Discount::fromArray()` |
| unsupported currency | `UnsupportedCurrencyException` | `Invoice::fromArray()` |
| text that is not UTF-8 | `SerializationException` | the invoice cannot be encoded; the `JsonException` is `getPrevious()` |

The rules behind each message are in [Responses](../guide/responses.md#error-messages) and [Error handling](../guide/errors.md).
