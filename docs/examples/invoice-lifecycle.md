---
description: Create an invoice from an array with fromArray(), fetch it, build its links, delete it, and see the 404 afterwards.
---

# Example: invoice lifecycle

<ProjectSource name="invoice-lifecycle" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

An invoice from its first to its last request: built with `Invoice::fromArray()` from an order row, created, fetched with `getInvoice()`, linked with `htmlUrl()` and `pdfDownloadUrl()`, deleted with `deleteInvoice()`, and fetched and deleted again to show that a 404 is a response, not an exception.
## The code

<<< @/../examples/invoice-lifecycle/run.php

## Output

<<< @/../examples/invoice-lifecycle/expected-output.txt{text}

## What to notice

- **`fromArray()`** takes the JSON field names, nested arrays for the client, its address, the items and their discounts, and the currency as a code. Its rules for unexpected types are in [Building invoices](../guide/invoices.md#fromarray).
- **`getData()`** of the detail response is the decoded JSON exactly as the API sent it; the client does not map it back into an `Invoice`.
- **A 404 is a response.** After the delete, `getInvoice()` and `deleteInvoice()` return `isOk() === false` with status 404 and the API's message; nothing is thrown.
- **Ids are URL-encoded.** `2026/002` is sent as `2026%2F002`, so the slash cannot change the path.

::: warning Detail and delete format
The fake API answers detail with `{"status": "ok", "invoice": {...}}` and delete with `{"status": "ok"}`. These formats are assumed, not verified against the live chcemfakturu.sk API; see [API calls](../guide/api.md#delete-an-invoice).
:::
