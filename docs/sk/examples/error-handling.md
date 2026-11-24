---
description: Vyvolajte každú chybovú odpoveď a každú výnimku invoice-client proti simulovanému API a pozrite sa, ako vyzerajú.
---

# Príklad: spracovanie chýb

<ProjectSource name="error-handling" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

Všetky spôsoby, ako môže volanie zlyhať, vyvolané zámerne. Prvá polovica dostane odpoveď od API (alebo vôbec nič od `NullTransport`) a vráti odpoveď s `isOk() === false`; druhá polovica vyhadzuje výnimky a jediný `catch (InvoiceClientException $e)` spracuje všetky.

## Kód {#the-code}

<<< @/../examples/error-handling/run.php

## Výstup {#output}

<<< @/../examples/error-handling/expected-output.txt{text}

## Na čo si všimnúť {#what-to-notice}

| Prípad | Druh | Prečo |
| --- | --- | --- |
| zlý token | odpoveď, 401 | API odpovedalo; chybou je jeho `message` |
| faktúra bez položiek | odpoveď, 422 | chyba validácie API (simulované API odmietne faktúru bez položiek) |
| neznáma faktúra | odpoveď, 404 | API odpovedalo |
| chybová HTML stránka z proxy | odpoveď, 502 | telo nie je JSON: `Cannot parse response from server (HTTP 502)`, HTML je stále v `getBody()` |
| `NullTransport` | odpoveď, stav 0 | nič sa neodoslalo: `No request was sent` |
| server je nedostupný | `TransportException` | žiadna HTTP odpoveď; správa končí chybou cURL, ktorá sa líši podľa systému, preto príklad vypíše len jej začiatok |
| cieľ bez schémy | `InvalidValueException` | vyhodí ju konštruktor |
| nečíselná cena | `InvalidValueException` | vyhodí ju setter, ešte pred odoslaním |
| pevná zľava na celú faktúru | `UnsupportedDiscountException` | API podporuje pevné zľavy len na položkách |
| neznámy typ zľavy | `InvalidValueException` | `Discount::fromArray()` |
| nepodporovaná mena | `UnsupportedCurrencyException` | `Invoice::fromArray()` |
| text, ktorý nie je UTF-8 | `SerializationException` | faktúru nemožno zakódovať; `JsonException` je v `getPrevious()` |

Pravidlá za každou správou sú v [Odpovediach](../guide/responses.md#error-messages) a v [Spracovaní chýb](../guide/errors.md).
