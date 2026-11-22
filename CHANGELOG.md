# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Changed
- **BC break:** PHP 8.3 or newer is required (was PHP 5.5).
- **BC break:** `guzzlehttp/guzzle` upgraded from `^6.0` to `^7.9`.
- **BC break:** native parameter, return and property types everywhere (`declare(strict_types=1)` in all files). `TransportInterface::call()` is now `call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result`, custom transports must be updated. `fromArray()` methods require an array and ignore values of unexpected types (e.g. booleans, or an empty or non-numeric string for a numeric field).
- Numeric setters (prices, quantity, VAT, discount value) accept `int`, `float` or a numeric string, and codes (`number`, `id`, variable/constant symbol, `identifier`, VAT/tax number, `zip`, `tel`, `fax`) accept `string` or `int`, also under `strict_types`. Values are sent to the API exactly as given, as before. A non-numeric string for a numeric field throws `InvalidArgumentException`.
- `GuzzleTransport` no longer echoes the body of a 5xx response; it returns it in the `Result` together with the real status code (was always 500). It accepts an optional Guzzle `ClientInterface`.
- `CreateInvoiceResponse`: `getId()` returns a string; an error response without a message gives `'Unknown error'`; a JSON response that is not an object is reported as `'Cannot parse response from server'` instead of a PHP error.
- `Invoice::setDiscount()` throws `Invoice\Exception` (a subclass of `\Exception`) for flat discounts.
- `Serializer::encodeInvoice()` throws `JsonException` instead of returning `false` (e.g. for invalid UTF-8, which used to send an empty request body).
- CI moved from Travis CI to GitHub Actions (PHP 8.3 - 8.6, nightly 8.7), with PHP CS Fixer, PHPStan (level max) and `composer audit`.
- PHPUnit 12; `codeclimate/php-test-reporter` and PHP_CodeSniffer removed in favour of PHP CS Fixer.
- Removed the manual `test.php` script.

### Added
- `InvoiceApi::getLogger()`.
- `Client::fromArray()` reads `identifier`.

### Fixed
- `Serializer::encodeAddress()` sent the zip code as `fax`.
- `InvoiceItem::fromArray()` stored `description` as `price_total`.
- `GuzzleTransport` imported a non-existent Symfony exception class.
- `Invoice::setItems()` returns the invoice, so it can be chained.
- Dynamic properties in `InvoiceApi` and `CreateInvoiceResponse` (deprecated since PHP 8.2).
