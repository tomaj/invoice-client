<p align="center">
  <a href="https://github.com/tomaj/invoice-client">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="docs/public/logo-dark.svg">
      <img src="docs/public/logo.svg" alt="Invoice Client" width="96" height="96">
    </picture>
  </a>
</p>

# Invoice Client

**PHP client for the chcemfakturu.sk invoicing API: create, fetch and delete invoices.**

> [!WARNING]
> **This repository is archived and no longer maintained.** The chcemfakturu.sk service it was written for has shut down (its domain no longer resolves), so the client has nothing to talk to. No version was ever released, and there will be no 1.0.0. The documentation sources stay in [`docs/`](docs/); the published site has been taken down.

[![Latest Stable Version](https://img.shields.io/packagist/v/tomaj/invoice-client.svg)](https://packagist.org/packages/tomaj/invoice-client)
[![CI](https://github.com/tomaj/invoice-client/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/tomaj/invoice-client/actions/workflows/ci.yml?query=branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/tomaj/invoice-client/badge.svg?branch=master)](https://coveralls.io/github/tomaj/invoice-client?branch=master)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/tomaj/invoice-client/badge)](https://scorecard.dev/viewer/?uri=github.com/tomaj/invoice-client)
[![License](https://img.shields.io/packagist/l/tomaj/invoice-client.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%5E8.4-777bb4.svg)](https://www.php.net/)

## What is invoice-client?

A small, typed PHP client for the chcemfakturu.sk API:

- build invoices with items, client, addresses and discounts (fluent setters or `fromArray()`),
- create, fetch and delete invoices, and build links to their HTML and PDF versions,
- get every API answer as a response object with the status, the error message and the raw body,
- test your integration without the network (`TestTransport`) and log requests through any PSR-3 logger.

## Installation

Requires PHP 8.4 or newer.

```bash
composer require tomaj/invoice-client
```

No version is tagged yet: until 1.0.0 is released, require `tomaj/invoice-client:dev-master`. If you used `dev-master` with the old `Invoice\` namespace, see [UPGRADE-1.0.md](UPGRADE-1.0.md).

## Usage

```php
use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;

$api = new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'));

$invoice = new Invoice()
    ->setName('Invoice 2024001')
    ->setCurrency(Currency::EUR)
    ->addItem(new InvoiceItem()->setDescription('Consulting')->setQuantity(10)->setPrice('40.00')->setVat(20));

$response = $api->createInvoice($invoice);

if ($response->isOk()) {
    echo 'PDF: ' . $response->getDownloadUrl() . "\n";
} else {
    echo 'Error ' . $response->getStatusCode() . ': ' . $response->getErrorMessage() . "\n";
}
```

All invoice fields, `getInvoice()` / `deleteInvoice()`, error handling, logging, timeouts and testing are described in the documentation sources in [`docs/`](docs/).

## Examples

[`examples/`](examples/) holds six runnable recipes (create an invoice, the create/fetch/delete lifecycle, error handling, logging, retries and timeouts, PHPUnit tests with `TestTransport`). They run against a local fake API started with `php -S`, need no account or token, and CI runs each one on every push: `examples/ci/recipe.sh create-invoice`.

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) and the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

Do not report security vulnerabilities in public issues. See [SECURITY.md](SECURITY.md), which also covers handling of the API token.

## License

MIT. See [LICENSE.md](LICENSE.md).
