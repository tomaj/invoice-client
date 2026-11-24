---
description: What a PSR-3 logger receives from InvoiceApi for a successful call, an API error and a connection failure, and proof that the token is never logged.
---

# Example: logging

<ProjectSource name="logging" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

A tiny PSR-3 logger that prints every record shows what `InvoiceApi` logs. In your application pass Monolog or your framework's logger instead; see [Logging](../guide/logging.md).
## The code

<<< @/../examples/logging/run.php

## Output

<<< @/../examples/logging/expected-output.txt{text}

## What to notice

- Every request is logged at `debug` before it is sent, and its result at `info` (OK) or `warning` (API error) with the status code.
- A connection failure is logged at `error` with the `TransportException` in the context, and then the exception is thrown to your code.
- The warning message has a PSR-3 placeholder, `Invoice API error: {error}`; the logger above fills it in, as Monolog's `PsrLogMessageProcessor` does.
- The last two lines check the whole log: neither the token nor any invoice text is in it.

All levels, messages and context keys are listed in [Logging](../guide/logging.md).
