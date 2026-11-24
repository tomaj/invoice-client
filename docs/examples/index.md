---
description: Six runnable invoice-client recipes against a local fake chcemfakturu.sk API, run in CI on every push.
---

# Examples

Complete, runnable recipes. Each one lives in [`examples/`](https://github.com/tomaj/invoice-client/tree/master/examples) as a small Composer project with a `run.php` (or PHPUnit tests) and an `expected-output.txt`. They talk to a [fake chcemfakturu.sk API](./fake-api.md) started with the PHP built-in web server, so they need no network, no account and no token, and never create real invoices.

The [`Examples` workflow](https://github.com/tomaj/invoice-client/blob/master/.github/workflows/examples.yml) installs each example against the code of every push and pull request, runs it and compares the output with `expected-output.txt` line by line. The code and output on these pages are included from those files, so what you read is what CI runs.

<LinkCards :items="[
  { title: 'Create an invoice', text: 'A complete invoice with client, addresses, items and discounts; the response, the links and what the API received.', link: '/examples/create-invoice' },
  { title: 'Invoice lifecycle', text: 'fromArray(), create, fetch, links, delete, and the 404 afterwards.', link: '/examples/invoice-lifecycle' },
  { title: 'Error handling', text: 'Every error response and every exception of the package, triggered on purpose.', link: '/examples/error-handling' },
  { title: 'Logging', text: 'Every PSR-3 record of a successful call, an API error and a connection failure.', link: '/examples/logging' },
  { title: 'Retries and timeouts', text: 'A custom Guzzle client: retry middleware for GET and DELETE, a timeout, an extra header.', link: '/examples/retries-and-timeouts' },
  { title: 'Testing with PHPUnit', text: 'Tests of your own service with TestTransport and Guzzle MockHandler.', link: '/examples/testing' },
]" />

## Running an example

You need PHP 8.4+, Composer and the ports 8701-8705 free. From a clone of the repository:

```bash
examples/ci/recipe.sh create-invoice
```

`recipe.sh` copies the example to a temporary directory, installs your checkout as `tomaj/invoice-client` through a Composer path repository, starts the fake API, runs the example and diffs the output. After an intended change of the output, `UPDATE_EXPECTED=1 examples/ci/recipe.sh <example>` rewrites `expected-output.txt`.

In your own project, copy `run.php`, replace `'test-token'` with your token from the environment and drop the `target` argument to talk to chcemfakturu.sk.
