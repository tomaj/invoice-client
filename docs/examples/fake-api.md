---
description: The fake chcemfakturu.sk API the examples run against - a single PHP file for the built-in web server, its endpoints, answers and simulated failures.
---

# The fake API

The examples talk to `examples/fake-api/server.php`, a single-file fake of the chcemfakturu.sk API for the PHP built-in web server. Copy it for end-to-end tests of your own application.

```bash
FAKE_API_STATE=/tmp/invoices.json php -S 127.0.0.1:8701 examples/fake-api/server.php
```

```php
$api = new InvoiceApi('test-token', target: 'http://127.0.0.1:8701');
```

## What it answers

| Request | Answer |
| --- | --- |
| any `/api/…` request without `Authorization: Bearer test-token` | `401` `{"status":"error","message":"Invalid API token"}` |
| `POST /api/v1/invoices/create` | `200` `{"status":"ok","id":"inv-0001","html_url":"…/html/inv-0001","download_pdf_url":"…/pdf-download/inv-0001"}` |
| create without `Content-Type: application/json` | `415` `{"status":"error","message":"The request body must be sent with Content-Type: application/json"}` |
| create with a body that is not `{"invoice": {...}}` | `400` with a message |
| create without items | `422` `{"status":"error","message":"The invoice has no items"}` |
| create with a currency the API does not know | `422` `{"status":"error","message":"Unsupported currency"}` |
| `GET /api/v1/invoices/detail/{id}` | `200` `{"status":"ok","invoice":{...}}`, or `404` `Invoice not found` |
| `DELETE /api/v1/invoices/delete/{id}` | `200` `{"status":"ok"}`, or `404` `Invoice not found` |
| a wrong HTTP method | `405` `Method not allowed` |
| `GET /html/{id}`, `/pdf/{id}`, `/pdf-download/{id}` | a small HTML page or a fake PDF |

Ids are sequential (`inv-0001`, `inv-0002`, …) and the invoices are kept in the JSON file `FAKE_API_STATE`, so a fresh state file gives the same ids on every run.

::: warning Not the real API
The create answer follows the documented API. The detail and delete answers, the validation rules and the messages are made up for the examples: the real formats of detail and delete are not verified ([#12](https://github.com/tomaj/invoice-client/issues/12)). Use the fake API to test your code's handling of answers, not to learn what chcemfakturu.sk accepts.
:::

## Simulated failures

A prefix in front of the path simulates a broken network or server:

| Prefix | Behaviour | Used by |
| --- | --- | --- |
| `/outage/…` | `502` with an HTML page, like a proxy in front of a broken server | [error handling](./error-handling.md) |
| `/flaky/…` | `503` for the first two requests, then the normal answer | [retries and timeouts](./retries-and-timeouts.md) |
| `/slow/…` | waits 3 seconds, then the normal answer | [retries and timeouts](./retries-and-timeouts.md) |

Pass the prefix as part of the target: `new InvoiceApi('test-token', target: 'http://127.0.0.1:8701/flaky')`.

## The code

<<< @/../examples/fake-api/server.php

## The test helpers

`examples/ci/lib.sh` starts the server with an empty state for each example and compares the output; `recipe.sh` and `use-local-invoice-client.sh` install the example against the current checkout.

<<< @/../examples/ci/lib.sh{bash}
