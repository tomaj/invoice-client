---
layout: home

hero:
  name: Invoice Client
  text: chcemfakturu.sk from PHP
  tagline: Create, fetch and delete invoices through the chcemfakturu.sk API, with typed invoices and responses you can test without the network.
  image:
    src: /logo.svg
    alt: Invoice Client
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: API Reference
      link: /api/reference
    - theme: alt
      text: View on GitHub
      link: https://github.com/tomaj/invoice-client

features:
  - title: Typed invoices
    details: Invoice, InvoiceItem, Client, Address and Discount with fluent setters or fromArray(); Currency and DiscountType are enums.
  - title: Exact payloads
    details: Prices and codes are sent exactly as you give them, so "40.00" stays "40.00" and a variable symbol keeps its leading zeros.
  - title: Errors as responses
    details: Every call returns a response with isOk(), the HTTP status, the API error message and the raw body; only a missing HTTP response throws.
  - title: Testable
    details: TestTransport records requests and returns queued responses; NullTransport sends nothing; GuzzleTransport takes your own Guzzle 7 or 8 client.
  - title: PSR-3 logging
    details: Pass any PSR-3 logger to see requests, status codes and API errors. The API token and invoice data are never logged.
  - title: PHP 8.4+
    details: Final classes, enums, one package exception interface, PHPStan level max, full coverage and mutation testing.
---
