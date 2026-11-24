---
description: Security of invoice-client - the API token, HTTPS and TLS verification, personal data in invoices, and how to report a vulnerability.
---

# Security

## Reporting a vulnerability

Do not open a public issue for a security problem. Report it privately through [GitHub private vulnerability reporting](https://github.com/tomaj/invoice-client/security/advisories/new), or by email as described in the [security policy](https://github.com/tomaj/invoice-client/blob/master/SECURITY.md). Never include a real API token or real customer data in a report.

## Supported versions

| Version | Supported |
| --- | --- |
| 1.x | yes, once 1.0.0 is released: bug fixes and security fixes |
| `dev-master` before 1.0.0 | no: upgrade to 1.0.0 ([upgrade guide](../upgrade.md)) |

## The API token

The token is a bearer secret: anyone who has it can create, read and delete invoices of your account.

- Keep it in the environment or a secret store, never in the repository or in front-end code.
- The client sends it only in the `Authorization` header. It is never passed to the PSR-3 logger, and the constructor argument is marked `#[\SensitiveParameter]`, so it is hidden in stack traces.
- Do not enable Guzzle's `debug` option or log full requests in production: that would print the header.
- Rotate the token in your account settings if it leaked.

## HTTPS and TLS

- The default target is `https://chcemfakturu.sk`. The client also accepts an `http://` target, for a local test server only: over plain HTTP the token travels in clear text.
- Keep TLS verification on. If you pass your own Guzzle client to `GuzzleTransport`, never set `verify` to `false`.

## Personal data

Invoices hold names, addresses, e-mails and tax numbers of your customers.

- The client logs only the HTTP method, the URL (which contains the invoice id), the status code and the API's error message, never request or response bodies.
- `getBody()` of a response may contain invoice data; keep it out of shared logs.
- A custom transport or logger must follow the same rules.

## In tests

Use `TestTransport`, a Guzzle `MockHandler` or the local fake API from the [examples](../examples/). Do not run tests with a real token: they would create real invoices.
