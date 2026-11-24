---
description: PHPUnit tests of an application service that uses invoice-client, with TestTransport and Guzzle MockHandler, no network.
---

# Example: testing with PHPUnit

<ProjectSource name="testing" :files="['src/OrderInvoicer.php', 'tests/OrderInvoicerTest.php', 'phpunit.xml', 'expected-output.txt', 'e2e.sh']" />

Your code is what needs tests, not the client. `OrderInvoicer` is a typical application service: it turns a paid order into an invoice and returns the PDF link, or throws its own exception when the API refuses. The tests check what it sends and how it handles each answer, without any network.
## The service

<<< @/../examples/testing/src/OrderInvoicer.php

## The tests

<<< @/../examples/testing/tests/OrderInvoicerTest.php

## Output

The test list, as `phpunit --testdox` prints it (`e2e.sh` drops the version, time and memory lines):

<<< @/../examples/testing/expected-output.txt{text}

## What to notice

- **`TestTransport`** returns the queued answers in order and records every request: method, URL, token and JSON payload. Decode the payload to assert what your code sent.
- **No queued answer** means no request: the response reports status `0` and `No request was sent`. That tests your code's reaction to invoicing being unavailable.
- **`MockHandler`** goes one level lower and runs the real `GuzzleTransport`, so you can assert HTTP headers such as `Authorization`.

More in [Testing your integration](../guide/testing.md).
