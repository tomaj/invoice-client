---
description: Testujte kód, ktorý používa invoice-client, bez siete - TestTransport, NullTransport, Guzzle MockHandler a lokálne simulované API pre end-to-end testy.
---

# Testovanie integrácie

Štyri úrovne, od najrýchlejšej po najrealistickejšiu. Žiadna z nich nepotrebuje API token ani sieť.

| Úroveň | Nástroj | Overí |
| --- | --- | --- |
| Rozhodnutia vášho kódu | `TestTransport` | čo váš kód posiela a ako reaguje na každú odpoveď |
| Žiadna fakturácia | `NullTransport` | vašu chybovú vetvu, napr. v stagingu |
| HTTP požiadavka | `GuzzleTransport` + Guzzle `MockHandler` | hlavičky, metódu, URL a telo tak, ako idú po sieti |
| End to end | lokálne simulované API cez `php -S` | celý stack cez skutočné HTTP |

## TestTransport

<<< @/snippets/test-transport.php

Výstup:

<<< @/snippets/test-transport.php.out{text}

| Metóda | Robí |
| --- | --- |
| `addResponse(Result $response): self` | zaradí do fronty odpoveď na ďalšie volanie; pre viac volaní ich zaraďte viac |
| `getRequests(): list<array>` | všetky doterajšie požiadavky, od najstaršej |
| `getLastRequest(): ?array` | posledná požiadavka, `null` pred prvou |

Zaznamenaná požiadavka je pole:

| Kľúč | Typ | Hodnota |
| --- | --- | --- |
| `method` | `HttpMethod` | `HttpMethod::Post` pre vytvorenie, `Get` pre detail, `Delete` pre zmazanie |
| `url` | `string` | celá URL |
| `apiToken` | `string` | token odovzdaný do `InvoiceApi` |
| `payload` | `?string` | telo JSON pri vytvorení, `null` pri detaile a zmazaní |

Volanie bez odpovede vo fronte vráti `null`, rovnako ako `NullTransport`: odpoveď hlási stav `0` a `No request was sent`. Takto rýchlo otestujete, ako sa váš kód správa, keď fakturácia nie je dostupná.

## Guzzle MockHandler

Ak chcete overiť samotnú HTTP požiadavku, dajte skutočnému `GuzzleTransport` Guzzle klienta, ktorého handler je [`MockHandler`](https://docs.guzzlephp.org/en/stable/testing.html), a pridajte history middleware, ktorý zaznamená, čo sa odoslalo:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;

$history = [];
$stack = HandlerStack::create(new MockHandler([
    new Response(200, [], '{"status":"ok","id":"inv-2"}'),
]));
$stack->push(Middleware::history($history));

$api = new InvoiceApi('test-token', transport: new GuzzleTransport(new Client(['handler' => $stack])));
$api->createInvoice($invoice);

$history[0]['request']->getHeaderLine('Authorization'); // 'Bearer test-token'
$history[0]['request']->getHeaderLine('Content-Type');  // 'application/json'
```

## PHPUnit test vašej služby {#a-phpunit-test-of-your-service}

[Príklad testing](../examples/testing.md) je malá aplikačná služba `OrderInvoicer`, ktorá z objednávky vytvorí faktúru, s PHPUnit testami, ktoré používajú obe techniky:

<<< @/../examples/testing/tests/OrderInvoicerTest.php

## End to end proti simulovanému API {#end-to-end-against-a-fake-api}

[Príklady](../examples/) bežia proti `examples/fake-api/server.php`, jednosúborovej simulácii API chcemfakturu.sk pre vstavaný webový server PHP. Na end-to-end testy si ho skopírujte do svojho projektu: spustite ho cez `php -S 127.0.0.1:8701 server.php` a odovzdajte `target: 'http://127.0.0.1:8701'`. Čo odpovedá, popisuje [Simulované API](../examples/fake-api.md).

## Čo testovať {#what-to-test}

- faktúru, ktorú váš kód zostaví z vašich dát: overte dekódovaný `payload` z `getLastRequest()`;
- chybovú vetvu: zaraďte do fronty `422` so `message`, `500` s HTML telom a žiadnu odpoveď;
- že váš kód si z úspešnej odpovede uloží `getId()` a odkazy;
- že politika opakovania neopakuje `createInvoice()`.

Testy nikdy nesmerujte na skutočné API so skutočným tokenom: každé spustenie testov by vytvorilo skutočné faktúry vo vašom účte.
