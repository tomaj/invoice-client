# FAQ

## Why does an API error not throw an exception?

So you always get the HTTP status, the message and the body of the error, and one `if ($response->isOk())` handles every failure the API reports. Exceptions are reserved for requests that got no answer (`TransportException`) and for invalid input. See [API Calls and Responses](./guide/api.md#responses).

## Where do I find my API token?

In the settings of your chcemfakturu.sk account. Store it outside the repository (environment variable, secret store) and never log it.

## Can I test my integration without calling the API?

Yes: pass a `TestTransport`, queue the responses, and check the requests your code sent. See [Testing your code](./guide/transports.md#testing-your-code).

## How do I set a timeout or a proxy?

Pass your own Guzzle client to `GuzzleTransport`; see [GuzzleTransport](./guide/transports.md#guzzletransport). The default client has a 10 second timeout.

## Why is my price sent as a string?

Because you gave it as a string. The client sends numbers exactly as given, so `"40.00"` stays `"40.00"` and `40.0` is sent as `40.0`. Pass an `int` or a `float` if you want a JSON number.

## How do I give a flat discount on the whole invoice?

The API does not support it. Add an item with a negative price, or give a flat discount on an item.

## I used dev-master with the `Invoice\` namespace. What changed?

The classes moved to `Tomaj\InvoiceClient\`, the constructor of `InvoiceApi` takes named arguments instead of an options array, currencies and discount types are enums, and API errors come back as responses. [UPGRADE-1.0.md](https://github.com/tomaj/invoice-client/blob/master/UPGRADE-1.0.md) lists every change with before/after code.
