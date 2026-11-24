---
description: Test code that uses invoice-client without the network - TestTransport, NullTransport, Guzzle MockHandler, and a local fake API for end-to-end tests.
---

# Testing your integration

Four levels, from the fastest to the most realistic. None of them needs an API token or the network.

| Level | Tool | Checks |
| --- | --- | --- |
| Your code's decisions | `TestTransport` | what your code sends and how it reacts to each answer |
| No invoicing at all | `NullTransport` | your error path, e.g. in staging |
| The HTTP request | `GuzzleTransport` + Guzzle `MockHandler` | headers, method, URL and body on the wire |
| End to end | a local fake API with `php -S` | the whole stack over real HTTP |

## TestTransport

<<< @/snippets/test-transport.php

Output:

<<< @/snippets/test-transport.php.out{text}

| Method | Does |
| --- | --- |
| `addResponse(Result $response): self` | queues the answer of the next call; queue several for several calls |
| `getRequests(): list<array>` | every request so far, oldest first |
| `getLastRequest(): ?array` | the last request, `null` before the first |

A recorded request is an array:

| Key | Type | Value |
| --- | --- | --- |
| `method` | `HttpMethod` | `HttpMethod::Post` for create, `Get` for detail, `Delete` for delete |
| `url` | `string` | the full URL |
| `apiToken` | `string` | the token given to `InvoiceApi` |
| `payload` | `?string` | the JSON body of create, `null` for detail and delete |

A call without a queued response returns `null`, like `NullTransport`: the response reports status `0` and `No request was sent`. That is a quick way to test your code's behaviour when invoicing is unavailable.

## Guzzle MockHandler

To check the HTTP request itself, give the real `GuzzleTransport` a Guzzle client whose handler is a [`MockHandler`](https://docs.guzzlephp.org/en/stable/testing.html), with a history middleware to record what was sent:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;

$history = [];
$stack = HandlerStack::create(new MockHandler([
    new Response(200, [], '{"status":"ok","id":"inv-2"}'),
]));
$stack->push(Middleware::history($history));

$api = new InvoiceApi('test-token', transport: new GuzzleTransport(new Client(['handler' => $stack])));
$api->createInvoice($invoice);

$history[0]['request']->getHeaderLine('Authorization'); // 'Bearer test-token'
```

## A PHPUnit test of your service

The [testing example](../examples/testing.md) is a small application service, `OrderInvoicer`, that turns an order into an invoice, with PHPUnit tests using both techniques:

<<< @/../examples/testing/tests/OrderInvoicerTest.php

## End to end against a fake API

The [examples](../examples/) run against `examples/fake-api/server.php`, a single-file fake of the chcemfakturu.sk API for the PHP built-in web server. Copy it into your project for end-to-end tests: start it with `php -S 127.0.0.1:8701 server.php` and pass `target: 'http://127.0.0.1:8701'`. See [The fake API](../examples/fake-api.md) for what it answers.

## What to test

- the invoice your code builds from your data: assert the decoded `payload` of `getLastRequest()`;
- the error path: queue a `422` with a `message`, a `500` with an HTML body, and no response at all;
- that your code stores `getId()` and the links of a successful answer;
- that a retry policy does not retry `createInvoice()`.

Never point tests at the real API with a real token: a test run would create real invoices of your account.
