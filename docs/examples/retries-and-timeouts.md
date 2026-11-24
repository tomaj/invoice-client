---
description: Give GuzzleTransport your own Guzzle client with retry middleware, a timeout and an extra header, and run it against a failing and a slow fake API.
---

# Example: retries and timeouts

<ProjectSource name="retries-and-timeouts" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

`GuzzleTransport` takes any Guzzle client, so everything Guzzle can do is available: here a retry middleware, a 2 second timeout and a `User-Agent` header. The fake API simulates a server that fails twice with 503 (`/flaky`) and one that needs 3 seconds (`/slow`).
## The code

<<< @/../examples/retries-and-timeouts/run.php

## Output

<<< @/../examples/retries-and-timeouts/expected-output.txt{text}

## What to notice

- **Retries live in Guzzle.** `Middleware::retry()` decides per request. `GuzzleTransport` sets `http_errors` to `false`, so a 503 reaches the decider as a response, and after the retries the final answer is a normal invoice-client response.
- **Never retry `POST` blindly.** A create request that timed out may have created the invoice; retrying it can create a duplicate. The decider retries only `GET` and `DELETE`.
- **A timeout is a `TransportException`.** There is no HTTP response, so the call throws; the Guzzle exception is `getPrevious()` (`NetworkTimeoutException` in Guzzle 8, a `ConnectException` in Guzzle 7).
- **Default headers** of your client are merged with the `Authorization` header the transport adds.

See [Transports](../guide/transports.md#your-own-guzzle-client) for the options worth setting.
