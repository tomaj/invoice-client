# Getting Started

invoice-client is a PHP client for the invoicing API of [chcemfakturu.sk](https://chcemfakturu.sk): it creates invoices, fetches and deletes them, and builds the links to their HTML and PDF versions.

## Requirements

- PHP 8.4 or newer
- `guzzlehttp/guzzle` 7.15.2+ or 8 and `psr/log` 2 or 3 (installed with the package)
- an API token of your chcemfakturu.sk account

## Installation

```bash
composer require tomaj/invoice-client
```

::: info Before 1.0.0
Before the 1.0.0 release only `dev-master` exists: `composer require tomaj/invoice-client:dev-master`. If you used `dev-master` before the `Tomaj\InvoiceClient\` namespace, follow [UPGRADE-1.0.md](https://github.com/tomaj/invoice-client/blob/master/UPGRADE-1.0.md).
:::

## First invoice

<<< @/snippets/getting-started.php

`createInvoice()` sends the invoice and returns a `CreateInvoiceResponse`. An error of the API (an invalid token, a validation error, a server error) is not an exception: `isOk()` is `false` and `getErrorMessage()` says why. See [API Calls and Responses](./api.md).

Keep the token out of your code: read it from the environment or your secret store. The token is a bearer secret; anyone who has it can manage the invoices of your account.

## Next steps

- [Building Invoices](./invoices.md): every field of an invoice, items, client, addresses, discounts and currencies.
- [API Calls and Responses](./api.md): fetching and deleting invoices, URLs, errors, logging.
- [Transports and Testing](./transports.md): timeouts, your own Guzzle client, testing without the API.
