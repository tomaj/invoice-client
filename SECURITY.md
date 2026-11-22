# Security Policy

## Supported Versions

| Version | Supported |
| ------- | --------- |
| 1.x     | Yes, once 1.0.0 is released: bug fixes and security fixes |
| dev-master before 1.0.0 | No: upgrade to 1.0.0 |

invoice-client has no tagged release yet; 1.0.0 will be the first. Until then, only the current `master` branch receives fixes.

## Reporting a Vulnerability

Do not open a public GitHub issue for a security vulnerability.

The preferred way to report a vulnerability is [GitHub private vulnerability reporting](https://github.com/tomaj/invoice-client/security/advisories/new).

If you cannot use private vulnerability reporting, email the details to **tomasmajer@gmail.com**.

Please include:

- A description of the vulnerability
- Steps to reproduce, or a proof of concept
- The invoice-client version
- The potential impact
- A suggested fix, if you have one

Never include a real API token or real customer data.

### Response

You will receive an acknowledgement within 14 days.

### Coordinated disclosure

We follow coordinated disclosure. Please give us time to prepare a fix before discussing the issue in public.

When a fix is ready, we publish a [GitHub Security Advisory](https://github.com/tomaj/invoice-client/security/advisories) and record the security fix in the [CHANGELOG](CHANGELOG.md).

## Security considerations

- **The API token is a bearer secret.** Anyone who has it can create, read and delete invoices of your account. Keep it in the environment or a secret store, never in the repository, and never log it. The client sends it only in the `Authorization` header and never passes it to the PSR-3 logger.
- **Use an `https://` target.** The default target is `https://chcemfakturu.sk`. An `http://` target (allowed for local testing) sends the token in clear text.
- **Keep TLS verification on.** If you pass your own Guzzle client to `GuzzleTransport`, do not set `verify` to `false`.
- **Invoices contain personal data** (names, addresses, VAT numbers). The client logs only the HTTP method, the URL, the status code and API error messages, never request or response bodies; keep it that way in custom transports and loggers.
