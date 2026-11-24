---
description: What happens between your call to InvoiceApi and the response object it returns, step by step.
---

# How it works

<Diagram name="flow" alt="Your code builds an Invoice and calls InvoiceApi, which serializes it to JSON and hands it to a transport; the transport sends it to the chcemfakturu.sk API with the bearer token and returns the HTTP status and body as a Result; InvoiceApi wraps it in a response object. A missing HTTP response throws TransportException; invalid input throws before any request." />

## The parts

| Part | Class | Job |
| --- | --- | --- |
| Data | `Invoice\Invoice`, `InvoiceItem`, `Client`, `Address`, `Discount` | hold the invoice; setters validate numbers and discounts |
| Encoding | `Serializer` | turns an `Invoice` into the JSON body `{"invoice": {...}}` |
| Client | `InvoiceApi` | builds the URL, calls the transport, logs, wraps the answer in a response |
| Transport | `Transport\GuzzleTransport`, `TestTransport`, `NullTransport` or your own | sends one HTTP request and returns its status and body as a `Result` |
| Answer | `Response\CreateInvoiceResponse`, `InvoiceDetailResponse`, `DeleteInvoiceResponse` | parse the JSON body, decide `isOk()`, expose the error message and data |

## A call, step by step

Take `$api->createInvoice($invoice)`:

1. **Encode.** `Serializer::encodeInvoice()` turns the invoice into JSON. Values are written exactly as you set them. A text that is not valid UTF-8 throws `SerializationException`; nothing is sent and nothing is logged.
2. **Log the request.** The PSR-3 logger gets `debug` `Invoice API request` with the method and URL.
3. **Send.** The transport receives the method (`HttpMethod::Post`), the URL (`{target}/api/v1/invoices/create`), the token and the JSON. `GuzzleTransport` sends it with `Authorization: Bearer <token>` and `Content-Type: application/json` and returns **every** HTTP response, 4xx and 5xx included, as a `Result`.
4. **No HTTP response?** DNS failure, refused connection, timeout or TLS error: the transport throws `TransportException`, the logger gets `error` `Invoice API request failed`, and the exception reaches your code.
5. **Parse.** The response object decodes the body. It is OK when the HTTP status is 2xx and the JSON has `"status": "ok"`. Otherwise the error message is the API's `message`, or one the client writes itself (see [Responses](./responses.md#error-messages)).
6. **Log the result.** `info` `Invoice API response` with the status code when OK; `warning` `Invoice API error: {error}` otherwise.
7. **Return.** You get the response object and decide what to do with `isOk()`.

`getInvoice($id)` and `deleteInvoice($id)` skip step 1: they send `GET {target}/api/v1/invoices/detail/{id}` and `DELETE {target}/api/v1/invoices/delete/{id}` without a body. The id is URL-encoded with `rawurlencode()`, so `2026/002` becomes `2026%2F002` and stays one path segment.

## Why API errors are not exceptions

An invalid token, a validation error and a server error are answers of the API. They come back as a response so you always have the HTTP status, the message and the raw body for your log or support ticket, and one `if ($response->isOk())` covers all of them. Exceptions are reserved for failures where there is no answer to look at: no HTTP response, an invoice that cannot be encoded, or a value that is wrong before anything is sent. [Error handling](./errors.md) lists them all.

## What the client does not do

- **No retries.** A failed request is reported once. Add retries for `GET`/`DELETE` with Guzzle middleware, see the [retries example](../examples/retries-and-timeouts.md). Do not blindly retry `createInvoice()`: a request that timed out may still have created the invoice.
- **No validation of invoice content.** Required fields, VAT rates and totals are checked by the API, which answers with an error message.
- **No issuer data.** The issuer of an invoice is the account the token belongs to; `Issuer` has no fields and is not sent.
