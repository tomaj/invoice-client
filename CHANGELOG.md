# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Changed
- **BC break:** PHP 8.3 or newer is required (was PHP 5.5).
- **BC break:** `guzzlehttp/guzzle` upgraded from `^6.0` to `^7.9`.
- **BC break:** native parameter, return and property types everywhere (`declare(strict_types=1)` in all files). `TransportInterface::call()` is now `call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result`, custom transports must be updated. `fromArray()` methods require an array and ignore values of unexpected types.
- `Invoice::setDiscount()` throws `Invoice\Exception` (a subclass of `\Exception`) for flat discounts.
- `Serializer::encodeInvoice()` throws `JsonException` instead of returning `false`.
- CI moved from Travis CI to GitHub Actions (PHP 8.3 - 8.6, nightly 8.7), with PHP CS Fixer, PHPStan (level max) and `composer audit`.
- PHPUnit 12; `codeclimate/php-test-reporter` and PHP_CodeSniffer removed in favour of PHP CS Fixer.

### Added
- `InvoiceApi::getLogger()`.
- `Client::fromArray()` reads `identifier`.

### Fixed
- `Serializer::encodeAddress()` sent the zip code as `fax`.
- `InvoiceItem::fromArray()` stored `description` as `price_total`.
- `GuzzleTransport` imported a non-existent Symfony exception class.
- Dynamic properties in `InvoiceApi` and `CreateInvoiceResponse` (deprecated since PHP 8.2).
