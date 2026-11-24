# Examples

Runnable recipes for tomaj/invoice-client, described on https://tomaj.github.io/invoice-client/examples/.
They talk to a fake chcemfakturu.sk API (`fake-api/server.php`, started with the PHP built-in web server),
so they need no network and no API token. The `Examples` workflow runs each one on every push and pull request.

Run one against this checkout (needs PHP 8.4+ and Composer; ports 8701-8705 must be free):

```bash
examples/ci/recipe.sh create-invoice
```

`recipe.sh` copies the example to a temporary directory, installs this checkout as `tomaj/invoice-client`
through a Composer path repository, starts the fake API and compares the output with `expected-output.txt`.
After an intended change, `UPDATE_EXPECTED=1 examples/ci/recipe.sh <example>` rewrites that file.

| Example | Shows |
| --- | --- |
| `create-invoice` | a complete invoice (client, addresses, items, discounts) created through the API |
| `invoice-lifecycle` | `Invoice::fromArray()`, create, fetch, links, delete, 404 after delete |
| `error-handling` | API errors as responses and every exception of the package |
| `logging` | what a PSR-3 logger receives, and that the token is never logged |
| `retries-and-timeouts` | a custom Guzzle client: retry middleware, timeout, extra header |
| `testing` | PHPUnit tests of your own code with `TestTransport` and Guzzle `MockHandler` |
