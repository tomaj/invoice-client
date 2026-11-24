# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased][unreleased]

Developers who used `dev-master` before the 1.0.0 work: [UPGRADE-1.0.md](https://github.com/tomaj/invoice-client/blob/master/UPGRADE-1.0.md) lists every change below with before/after code.

### Added
- `InvoiceApi::getInvoice()` and `deleteInvoice()` return `InvoiceDetailResponse` / `DeleteInvoiceResponse` ([#12](https://github.com/tomaj/invoice-client/issues/12)).
- Every response implements `ResponseInterface`: `isOk()`, `getStatusCode()`, `getErrorMessage()`, `getBody()` and `getData()` ([#13](https://github.com/tomaj/invoice-client/issues/13)).
- `Tomaj\InvoiceClient\Exception\InvoiceClientException`, implemented by every exception of the package: `TransportException`, `SerializationException`, `InvalidValueException`, `UnsupportedCurrencyException`, `UnsupportedDiscountException` ([#8](https://github.com/tomaj/invoice-client/issues/8)).
- The PSR-3 logger receives the method and URL of each request, the status code of each response, API error messages and transport failures; never the token or invoice data. `psr/log` `^2 || ^3` is a dependency ([#14](https://github.com/tomaj/invoice-client/issues/14)).
- `TestTransport` queues several responses (`addResponse()`) and records every request (`getRequests()`, `getLastRequest()`) ([#8](https://github.com/tomaj/invoice-client/issues/8)).
- `Client::fromArray()` reads `identifier` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- Documentation site at presentation level: own logo, favicons, illustrations and flow diagram, a page per topic (responses, error handling, logging, transports, testing, configuration, security, upgrade), a full API reference, and six runnable examples against a local fake API, run by the new `Examples` workflow ([#17](https://github.com/tomaj/invoice-client/issues/17)).
- `LICENSE.md` (MIT), `SECURITY.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `RELEASING.md` ([#4](https://github.com/tomaj/invoice-client/issues/4)); documentation site https://tomaj.github.io/invoice-client/ ([#10](https://github.com/tomaj/invoice-client/issues/10)).
- Slovak translation of the documentation site ([#21](https://github.com/tomaj/invoice-client/issues/21)).

### Changed
- `GuzzleTransport` sends the JSON body of `createInvoice()` with `Content-Type: application/json` (the body is unchanged; `GET` and `DELETE` have no body and no `Content-Type`). The fake API of the examples rejects a create request without it ([#19](https://github.com/tomaj/invoice-client/issues/19)).
- **BREAKING CHANGE**: classes moved from `Invoice\` to `Tomaj\InvoiceClient\` with PSR-4 autoloading: `Tomaj\InvoiceClient\InvoiceApi`, `Serializer`, `Invoice\*` (invoice, items, client, address, discount, currency), `Transport\*`, `Response\*`, `Exception\*` ([#8](https://github.com/tomaj/invoice-client/issues/8)).
- **BREAKING CHANGE**: `new InvoiceApi(string $apiToken, string $target = InvoiceApi::DEFAULT_TARGET, ?TransportInterface $transport = null, ?LoggerInterface $logger = null)` replaces the options array; the `debug` option and `getLogger()` are removed. An invalid target throws `InvalidValueException`, an invalid logger is a `TypeError` ([#8](https://github.com/tomaj/invoice-client/issues/8), [#14](https://github.com/tomaj/invoice-client/issues/14)).
- **BREAKING CHANGE**: `Currency` is a string-backed enum (`Invoice::setCurrency(?Currency)`, `getCurrency(): ?Currency`; `fromArray()` still takes the code); `Currency::isValid()` is removed. `Discount` takes a `DiscountType` enum and is readonly (no setters); `Discount::fromArray()` throws `InvalidValueException` for an unknown type ([#8](https://github.com/tomaj/invoice-client/issues/8)).
- **BREAKING CHANGE**: `TransportInterface::call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result` takes an `HttpMethod` enum as the first argument; transports throw `TransportException` when there is no HTTP response ([#8](https://github.com/tomaj/invoice-client/issues/8), [#12](https://github.com/tomaj/invoice-client/issues/12)).
- **BREAKING CHANGE**: `ResponseInterface::getErrorMessage()` returns `?string` (`null` when OK, was `false`). A response is OK only when the HTTP status is 2xx and the body has `"status": "ok"`. Unparsable bodies report `Cannot parse response from server (HTTP <status>)`, a call through `NullTransport` reports `No request was sent` ([#13](https://github.com/tomaj/invoice-client/issues/13)).
- **BREAKING CHANGE**: all classes are `final`; `Result` and `Discount` are readonly. `Serializer::encodeClient()`, `encodeAddress()`, `encodeItems()` are private and `Serializer::decodeInvoice()` is removed; `Serializer::encodeInvoice()` throws `SerializationException` (with the `JsonException` as previous). `TestTransport::setResponse()`, `getUrl()`, `getApiKey()`, `getPayload()`, `getMethod()` are replaced by `addResponse()`, `getRequests()` and `getLastRequest()` ([#8](https://github.com/tomaj/invoice-client/issues/8)).
- **BREAKING CHANGE**: numeric setters throw `InvalidValueException` (an `InvalidArgumentException`) and a flat invoice discount throws `UnsupportedDiscountException` (was `Invoice\Exception`) ([#8](https://github.com/tomaj/invoice-client/issues/8)).
- Invoice ids are URL-encoded in the API and HTML/PDF URLs ([#12](https://github.com/tomaj/invoice-client/issues/12)).
- **BREAKING CHANGE**: PHP 8.4 or newer is required (was PHP 5.5) ([#2](https://github.com/tomaj/invoice-client/pull/2), [#7](https://github.com/tomaj/invoice-client/issues/7)).
- **BREAKING CHANGE**: `guzzlehttp/guzzle` upgraded from `^6.0` to `^7.15.2 || ^8.0.1` (Guzzle 8 supported; the lower bounds exclude releases with published security advisories) ([#2](https://github.com/tomaj/invoice-client/pull/2), [#3](https://github.com/tomaj/invoice-client/pull/3)).
- **BREAKING CHANGE**: native parameter, return and property types everywhere (`declare(strict_types=1)` in all files). Custom transports must be updated. `fromArray()` methods require an array and ignore values of unexpected types (e.g. booleans, or an empty or non-numeric string for a numeric field) ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- Numeric setters (prices, quantity, VAT, discount value) accept `int`, `float` or a numeric string, and codes (`number`, `id`, variable/constant symbol, `identifier`, VAT/tax number, `zip`, `tel`, `fax`) accept `string` or `int`, also under `strict_types`. Values are sent to the API exactly as given, as before. A non-numeric string for a numeric field throws `InvalidArgumentException` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `GuzzleTransport` no longer echoes the body of a 5xx response; it returns it in the `Result` together with the real status code (was always 500). It accepts an optional Guzzle `ClientInterface` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `CreateInvoiceResponse`: `getId()` returns a string; an error response without a message gives `'Unknown error'`; a JSON response that is not an object is reported as `'Cannot parse response from server'` instead of a PHP error ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `Serializer::encodeInvoice()` throws an exception instead of returning `false` (e.g. for invalid UTF-8, which used to send an empty request body) ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- CI moved from Travis CI to GitHub Actions (PHP 8.4 - 8.6, nightly 8.7), with PHP CS Fixer, PHPStan (level max), Rector, Infection, a 90 % coverage threshold and `composer audit`; release, CodeQL and OpenSSF Scorecard workflows ([#2](https://github.com/tomaj/invoice-client/pull/2), [#5](https://github.com/tomaj/invoice-client/issues/5), [#6](https://github.com/tomaj/invoice-client/issues/6), [#9](https://github.com/tomaj/invoice-client/issues/9)).
- The Composer dist archive contains only the library (`src/`, `composer.json`, README, LICENSE, CHANGELOG and UPGRADE files) ([#4](https://github.com/tomaj/invoice-client/issues/4)).
- PHPUnit 13; `codeclimate/php-test-reporter` and PHP_CodeSniffer removed in favour of PHP CS Fixer ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- Removed the manual `test.php` script ([#2](https://github.com/tomaj/invoice-client/pull/2)).

### Fixed
- `getInvoice()` and `deleteInvoice()` always threw with the default `GuzzleTransport`, which only sent `POST`; it now sends `GET`, `POST` and `DELETE` ([#12](https://github.com/tomaj/invoice-client/issues/12)).
- A 4xx response made `GuzzleTransport` throw Guzzle's `ClientException`; every HTTP response is now returned as a response object with the API's error message ([#13](https://github.com/tomaj/invoice-client/issues/13)).
- An invalid `logger` option left `InvoiceApi` without a transport and target (`No transport configured`), and the logger was never used ([#14](https://github.com/tomaj/invoice-client/issues/14)).
- `Serializer::encodeAddress()` sent the zip code as `fax` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `InvoiceItem::fromArray()` stored `description` as `price_total` ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `GuzzleTransport` imported a non-existent Symfony exception class ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- `Invoice::setItems()` returns the invoice, so it can be chained ([#2](https://github.com/tomaj/invoice-client/pull/2)).
- Dynamic properties in `InvoiceApi` and `CreateInvoiceResponse` (deprecated since PHP 8.2) ([#2](https://github.com/tomaj/invoice-client/pull/2)).

[unreleased]: https://github.com/tomaj/invoice-client/commits/master
