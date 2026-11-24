---
description: Install tomaj/invoice-client, create your first invoice through the chcemfakturu.sk API and read the response.
---

# Getting started

invoice-client is a PHP client for the invoicing API of [chcemfakturu.sk](https://chcemfakturu.sk): it creates invoices, fetches and deletes them, and builds the links to their HTML and PDF versions. It is an independent open-source package, not an official product of chcemfakturu.sk.

## Requirements

- PHP 8.4 or newer (8.4, 8.5 and 8.6 are tested)
- `guzzlehttp/guzzle` 7.15.2+ or 8.0.1+ and `psr/log` 2 or 3, installed with the package
- an API token of your chcemfakturu.sk account

## Installation

```bash
composer require tomaj/invoice-client
```

::: info Before 1.0.0
No version is tagged yet. Until 1.0.0 is released, require `dev-master`:

```bash
composer require tomaj/invoice-client:dev-master
```

If you used `dev-master` before the `Tomaj\InvoiceClient\` namespace, follow the [upgrade guide](../upgrade.md).
:::

## Your first invoice

<<< @/snippets/getting-started.php

1. `new InvoiceApi($token)` creates the client. It talks to `https://chcemfakturu.sk` through Guzzle with a 10 second timeout; every argument is described in the [configuration reference](./configuration.md).
2. `Invoice`, `InvoiceItem` and the other classes of `Tomaj\InvoiceClient\Invoice\` hold the data. Every setter returns the object, so calls chain. See [Building invoices](./invoices.md).
3. `createInvoice()` sends `POST /api/v1/invoices/create` and returns a `CreateInvoiceResponse`.
4. `isOk()` is `true` only when the API answered with a 2xx status and `"status": "ok"`. Otherwise `getErrorMessage()` says why: an invalid token, a validation error or a server error is **not** an exception. See [Responses](./responses.md).

## What you get back

Without an account, try the same call against the fake API of the [examples](../examples/). The [create-invoice example](../examples/create-invoice.md) builds a complete invoice, creates it and prints the response, the links and the invoice as the API stored it. Its output, checked in CI on every push:

<<< @/../examples/create-invoice/expected-output.txt{text}

`getId()`, `getHtmlUrl()` and `getDownloadUrl()` come from the JSON answer of the API:

```json
{"status": "ok", "id": "inv-0001", "html_url": "https://…/html/inv-0001", "download_pdf_url": "https://…/pdf-download/inv-0001"}
```

`htmlUrl()`, `pdfUrl()` and `pdfDownloadUrl()` build the same kind of link from an id you stored earlier, without a request.

## Keep the token secret

The token is a bearer secret: anyone who has it can create, read and delete the invoices of your account. Read it from the environment or your secret store, never commit it, and keep the default `https://` target. The client sends it only in the `Authorization` header and never logs it. More in [Security](./security.md).

## Next steps

<LinkCards :items="[
  { title: 'How it works', text: 'The request flow from your code to the API and back.', link: '/guide/how-it-works' },
  { title: 'Building invoices', text: 'Every field, item, client, address, discount and currency.', link: '/guide/invoices' },
  { title: 'Error handling', text: 'Responses, exceptions and what to do with each.', link: '/guide/errors' },
  { title: 'Testing your integration', text: 'Test your code without the network.', link: '/guide/testing' },
]" />
