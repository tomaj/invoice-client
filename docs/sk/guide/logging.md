---
description: Čo InvoiceApi loguje cez PSR-3 logger - úrovne, správy a kľúče kontextu - a čo nikdy.
---

# Logovanie

Ako `logger` odovzdajte ľubovoľný [PSR-3](https://www.php-fig.org/psr/psr-3/) logger (Monolog, logger vášho frameworku):

```php
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Tomaj\InvoiceClient\InvoiceApi;

$logger = new Logger('invoices');
$logger->pushHandler(new StreamHandler('php://stderr'));
$logger->pushProcessor(new PsrLogMessageProcessor()); // doplní {error} do správy

$api = new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'), logger: $logger);
```

Bez loggera klient používa `Psr\Log\NullLogger`.

## Čo sa loguje {#what-is-logged}

| Kedy | Úroveň | Správa | Kontext |
| --- | --- | --- | --- |
| pred každou požiadavkou | `debug` | `Invoice API request` | `method`, `url` |
| odpoveď je OK | `info` | `Invoice API response` | `method`, `url`, `status` |
| odpoveď je chyba | `warning` | `Invoice API error: {error}` | `method`, `url`, `status`, `error` |
| žiadna HTTP odpoveď (`TransportException`) | `error` | `Invoice API request failed` | `method`, `url`, `exception` |

- `method` je `GET`, `POST` alebo `DELETE`; `url` je celá URL vrátane (do URL zakódovaného) id faktúry; `status` je HTTP stavový kód, `0`, keď sa nič neodoslalo.
- `error` je `getErrorMessage()` odpovede. Správa používa PSR-3 zástupný symbol, takže logger, ktorý ho doplní, ukáže `Invoice API error: Invalid API token`.
- `exception` je `TransportException`, zalogovaná predtým, ako sa vyhodí do vášho kódu. Logujte ju tam znova, len ak ju chcete mať dvakrát.
- Faktúra, ktorú nemožno zakódovať (`SerializationException`), sa nezaloguje: klient sa o požiadavku ani nepokúsil.

[Príklad logging](../examples/logging.md) vypíše každý záznam pre úspešné volanie, chybu API a zlyhanie spojenia:

<<< @/../examples/logging/expected-output.txt{text}

## Čo sa nikdy neloguje {#what-is-never-logged}

- **API token.** Je to bearer tajomstvo. V konštruktore je navyše označený `#[\SensitiveParameter]`, takže sa neobjaví ani v stack trace.
- **Telo požiadavky a telo odpovede.** Faktúry obsahujú mená, adresy a IČ DPH vašich odberateľov.

Dodržte to aj vo vlastnom kóde: logujte `getStatusCode()` a `getErrorMessage()` a `getBody()` si nechajte pre tiket na podporu, nie pre zdieľaný log. Ani vlastný transport nesmie logovať token ani dáta.

## Ladenie požiadavky {#debugging-a-request}

Presný JSON, ktorý posielate, uvidíte, keď faktúru zakódujete bez odoslania:

```php
use Tomaj\InvoiceClient\Serializer;

echo new Serializer()->encodeInvoice($invoice);
```

Surovú HTTP komunikáciu uvidíte pri vývoji, keď `GuzzleTransport` dáte Guzzle klienta s `'debug' => true` alebo s history middleware (pozrite [Transporty](./transports.md#your-own-guzzle-client)). V produkcii to nikdy nezapínajte: vypíše hlavičku `Authorization`.
