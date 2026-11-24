---
description: Frequently asked questions about invoice-client - errors, tokens, testing, timeouts, prices as strings, discounts, retries and the detail/delete format.
---

# FAQ

## Is this an official chcemfakturu.sk client?

No. It is an independent open-source package (MIT) by Tomas Majer that talks to the public API of chcemfakturu.sk. Questions about your account, prices or the API itself go to chcemfakturu.sk; questions about this package go to [GitHub issues](https://github.com/tomaj/invoice-client/issues).

## Why does an API error not throw an exception?

So you always get the HTTP status, the message and the body of the error, and one `if ($response->isOk())` handles every failure the API reports. Exceptions are reserved for requests that got no answer (`TransportException`) and for invalid input. See [Error handling](./guide/errors.md).

## Where do I find my API token?

In the settings of your chcemfakturu.sk account. Store it outside the repository (environment variable, secret store) and never log it. See [Security](./guide/security.md).

## Can I try the client without an account?

Yes. The [examples](./examples/) run against a fake API you start with `php -S`, and [`TestTransport`](./guide/testing.md#testtransport) needs no server at all.

## Can I test my integration without calling the API?

Yes: pass a `TestTransport`, queue the answers, and check the requests your code sent. For the HTTP level use a Guzzle `MockHandler`. See [Testing your integration](./guide/testing.md) and the [PHPUnit example](./examples/testing.md).

## How do I set a timeout, a proxy or extra headers?

Pass your own Guzzle client to `GuzzleTransport`. The default client has a 10 second timeout. See [Transports](./guide/transports.md#your-own-guzzle-client).

## Does the client retry failed requests?

No. Add a Guzzle retry middleware for `GET` and `DELETE` (the [retries example](./examples/retries-and-timeouts.md) shows one). Do not retry `createInvoice()` blindly: after a timeout the invoice may already exist.

## Why is my price sent as a string?

Because you gave it as a string. The client sends numbers exactly as given, so `"40.00"` stays `"40.00"` and `40.0` is sent as `40.0`. Pass an `int` or a `float` if you want a JSON number. See [Values are sent as given](./guide/invoices.md#values-are-sent-as-given).

## How do I give a flat discount on the whole invoice?

The API does not support it, so `Invoice::setDiscount()` throws `UnsupportedDiscountException`. Add an item with a negative price, or give a flat discount on an item.

## Where do I set the issuer of the invoice?

Nowhere: the issuer is the account the API token belongs to. `Issuer` exists for compatibility, has no fields and is not sent.

## What does the detail response contain?

Whatever the API sends: `getData()` is the decoded JSON object. The format of the detail and delete answers is assumed to follow the create endpoint and is not verified against the live API. If your account's API answers differently, the raw answer is in `getStatusCode()` and `getBody()`; please [open an issue](https://github.com/tomaj/invoice-client/issues).

## Why does isOk() return false for a 200 response?

`isOk()` needs both a 2xx status and `"status": "ok"` in a JSON object. A 200 with an HTML page, an empty body or `"status": "error"` is not OK; `getErrorMessage()` says which case it is. See [Responses](./guide/responses.md).

## Can I use another HTTP client than Guzzle?

Yes: implement `TransportInterface`. [Transports](./guide/transports.md#writing-a-transport) has a complete PSR-18 transport. Guzzle stays a dependency of the package.

## Which PHP versions are supported?

PHP 8.4 or newer; CI tests 8.4, 8.5 and 8.6, and the 8.7 nightly.

## I used dev-master with the `Invoice\` namespace. What changed?

The classes moved to `Tomaj\InvoiceClient\`, the constructor of `InvoiceApi` takes named arguments instead of an options array, currencies and discount types are enums, and API errors come back as responses. The [upgrade guide](./upgrade.md) lists every change with before/after code.
