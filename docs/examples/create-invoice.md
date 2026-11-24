---
description: Build a complete invoice with client, addresses, items and discounts, create it through the API and read the id and links.
---

# Example: create an invoice

<ProjectSource name="create-invoice" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

The full path of a real invoice: a client with a company id, tax and VAT numbers and an address, a shipping address, two items (one with a flat discount), a 5 % discount on the whole invoice, and the payment symbols. The example creates it, prints the response and the links, then fetches it back to show the JSON the API received.
## The code

<<< @/../examples/create-invoice/run.php

## Output

<<< @/../examples/create-invoice/expected-output.txt{text}

## What to notice

- **Values are sent as given.** `'40.00'` stays the string `"40.00"`, `120` stays a number, and the constant symbol `'0308'` keeps its leading zero because it is a string. Unset fields such as `status` and `price` are sent as `null`.
- **Discounts.** The flat discount sits on the licence item; the whole invoice gets a percent discount. A flat discount on the whole invoice would throw `UnsupportedDiscountException`.
- **Two ways to the links.** `getHtmlUrl()` and `getDownloadUrl()` return the links the API sent; `htmlUrl()`, `pdfUrl()` and `pdfDownloadUrl()` build them from an id you stored, without a request.
- **The token.** The fake API accepts only `test-token`. In production read the token from the environment and leave out `target`.

Every field is described in [Building invoices](../guide/invoices.md); the response getters in [Responses](../guide/responses.md#createinvoiceresponse).

## How it is tested

<<< @/../examples/create-invoice/e2e.sh{bash}

`fake_api` starts `examples/fake-api/server.php` on port 8701 with an empty state, so the first invoice is always `inv-0001`; `expect_output` diffs the output with `expected-output.txt`.
