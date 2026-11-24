---
layout: home
title: Invoice Client – PHP client for the chcemfakturu.sk API
titleTemplate: false
description: 'PHP client for the chcemfakturu.sk invoicing API: build typed invoices, create, fetch and delete them, handle every error as a response, log through PSR-3 and test without the network.'
hero:
  name: Invoice Client
  text: chcemfakturu.sk invoices from PHP
  tagline: 'Build an invoice with typed objects, send it with one call and get a response you can check: the id, the PDF link, or the exact error. Test it all without the network.'
  image:
    light: /illustrations/hero-light.svg
    dark: /illustrations/hero-dark.svg
    alt: An invoice with three items and a paid stamp, sent as JSON to the invoicing API, which answers with status ok
  actions:
    - theme: brand
      text: Get started
      link: /guide/getting-started
    - theme: alt
      text: Examples
      link: /examples/
    - theme: alt
      text: GitHub
      link: https://github.com/tomaj/invoice-client
features:
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="7" y="4" width="26" height="36" rx="5"/><path class="ic-muted" d="M13 13h14M13 20h14M13 27h8"/><circle class="ic-accf" cx="34" cy="34" r="10"/><path class="ic-mark" d="M29.5 34.3l3 3 6-6.3"/></svg>'
    title: Typed invoices
    details: Invoice, InvoiceItem, Client, Address and Discount with fluent setters or fromArray(). Currency and DiscountType are enums; a wrong value throws before anything is sent.
    link: /guide/invoices
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path class="ic-ink" d="M15 8c-5 0-5 3-5 7s-2 7-5 9c3 2 5 5 5 9s0 7 5 7M33 8c5 0 5 3 5 7s2 7 5 9c-3 2-5 5-5 9s0 7-5 7"/><path class="ic-acc" d="M19 24h.01M24 24h.01M29 24h.01"/></svg>'
    title: Exact payloads
    details: 'Prices and codes go to the API exactly as you give them: "40.00" stays "40.00" and a constant symbol keeps its leading zero.'
    link: /guide/invoices#values-are-sent-as-given
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path class="ic-paper" d="M6 10a5 5 0 0 1 5-5h26a5 5 0 0 1 5 5v18a5 5 0 0 1-5 5H20l-9 8v-8a5 5 0 0 1-5-5z"/><path class="ic-acc" d="M24 12v9"/><path class="ic-acc" d="M24 26.5h.01"/></svg>'
    title: Errors are responses
    details: Every call returns a response with isOk(), the HTTP status, the API message and the raw body. Only a request without any HTTP response throws.
    link: /guide/errors
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="6" y="6" width="36" height="36" rx="6"/><path class="ic-acc" d="M12 16l3 3 5-5M12 30l3 3 5-5"/><path class="ic-muted" d="M25 17h11M25 31h11"/></svg>'
    title: Testable
    details: TestTransport queues responses and records requests; NullTransport sends nothing; GuzzleTransport takes your own Guzzle client with a MockHandler, retries or timeouts.
    link: /guide/testing
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="5" y="6" width="30" height="32" rx="5"/><path class="ic-muted" d="M11 14h18M11 21h18M11 28h10"/><rect class="ic-accf" x="27" y="27" width="16" height="14" rx="3"/><path class="ic-acc" d="M30 27v-3a5 5 0 0 1 10 0v3"/><path class="ic-mark" d="M35 32v4"/></svg>'
    title: PSR-3 logging, no secrets
    details: Pass any PSR-3 logger to see requests, status codes and API errors. The API token and the invoice data never reach the log.
    link: /guide/logging
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="4" y="8" width="40" height="32" rx="5"/><path class="ic-ink" d="M4 16h40"/><path class="ic-acc" d="M11 24l5 4-5 4M20 32h9"/></svg>'
    title: Examples that run
    details: Six recipes talk to a local fake API started with php -S, and CI runs each of them against the current code on every push.
    link: /examples/
---

## What it looks like

<<< @/snippets/getting-started.php{php}

Every call goes through the same few steps; an API error comes back as a response, never as an exception:

<Diagram name="flow" alt="Your code builds an Invoice and calls InvoiceApi, which serializes it to JSON and hands it to a transport; the transport sends it to the chcemfakturu.sk API with the bearer token and returns the HTTP status and body as a Result; InvoiceApi wraps it in a response object. A missing HTTP response throws TransportException; invalid input throws before any request." />

## Where to go next

<LinkCards :items="[
  { title: 'Getting started', text: 'Install the package and create your first invoice.', link: '/guide/getting-started' },
  { title: 'Building invoices', text: 'Every field of an invoice, its items, client, addresses and discounts.', link: '/guide/invoices' },
  { title: 'Error handling', text: 'Which failures are responses and which are exceptions.', link: '/guide/errors' },
  { title: 'Examples', text: 'Six runnable recipes against a local fake API, tested in CI.', link: '/examples/' },
  { title: 'Testing your integration', text: 'TestTransport, NullTransport and Guzzle MockHandler.', link: '/guide/testing' },
  { title: 'API reference', text: 'Every public class and method.', link: '/api/reference' },
]" />
