# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased][unreleased]

### Changed
- **BREAKING CHANGE**: PHP 8.3 or newer is required (was PHP 5.5) ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- **BREAKING CHANGE**: `guzzlehttp/guzzle` upgraded from `^6.0` to `^7.9` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- **BREAKING CHANGE**: native parameter, return and property types everywhere (`declare(strict_types=1)` in all files). `TransportInterface::call()` is now `call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result`, custom transports must be updated. `fromArray()` methods require an array and ignore values of unexpected types (e.g. booleans, or an empty or non-numeric string for a numeric field) ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- Numeric setters (prices, quantity, VAT, discount value) accept `int`, `float` or a numeric string, and codes (`number`, `id`, variable/constant symbol, `identifier`, VAT/tax number, `zip`, `tel`, `fax`) accept `string` or `int`, also under `strict_types`. Values are sent to the API exactly as given, as before. A non-numeric string for a numeric field throws `InvalidArgumentException` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `GuzzleTransport` no longer echoes the body of a 5xx response; it returns it in the `Result` together with the real status code (was always 500). It accepts an optional Guzzle `ClientInterface` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `CreateInvoiceResponse`: `getId()` returns a string; an error response without a message gives `'Unknown error'`; a JSON response that is not an object is reported as `'Cannot parse response from server'` instead of a PHP error ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `Invoice::setDiscount()` throws `Invoice\Exception` (a subclass of `\Exception`) for flat discounts ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `Serializer::encodeInvoice()` throws `JsonException` instead of returning `false` (e.g. for invalid UTF-8, which used to send an empty request body) ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- CI moved from Travis CI to GitHub Actions (PHP 8.3 - 8.6, nightly 8.7), with PHP CS Fixer, PHPStan (level max) and `composer audit` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- PHPUnit 12; `codeclimate/php-test-reporter` and PHP_CodeSniffer removed in favour of PHP CS Fixer ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- Removed the manual `test.php` script ([#2](https://github.com/tomaj/invoice-client/pull/2)).

### Added
- `InvoiceApi::getLogger()` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `Client::fromArray()` reads `identifier` ([#2](https://github.com/tomaj/invoice-client/pull/2)).

### Fixed
- `Serializer::encodeAddress()` sent the zip code as `fax` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `InvoiceItem::fromArray()` stored `description` as `price_total` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `GuzzleTransport` imported a non-existent Symfony exception class ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `Invoice::setItems()` returns the invoice, so it can be chained ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- Dynamic properties in `InvoiceApi` and `CreateInvoiceResponse` (deprecated since PHP 8.2) ([#2](https://github.com/tomaj/invoice-client/pull/2)).

[unreleased]: https://github.com/tomaj/invoice-client/commits/master
