---
description: Časté otázky o invoice-client - chyby, tokeny, testovanie, timeouty, ceny ako reťazce, zľavy, opakovanie a formát detailu a zmazania.
---

# Časté otázky

## Je to oficiálny klient chcemfakturu.sk?

Nie. Je to nezávislý open-source balík (MIT) od Tomasa Majera, ktorý komunikuje s verejným API chcemfakturu.sk. Otázky k vášmu účtu, cenám alebo samotnému API patria chcemfakturu.sk; otázky k tomuto balíku do [GitHub issues](https://github.com/tomaj/invoice-client/issues).

## Prečo chyba API nevyhodí výnimku?

Aby ste vždy dostali HTTP stav, správu a telo chyby a jedno `if ($response->isOk())` spracovalo každé zlyhanie, ktoré API ohlási. Výnimky sú vyhradené pre požiadavky, na ktoré neprišla žiadna odpoveď (`TransportException`), a pre neplatný vstup. Pozrite si [Spracovanie chýb](./guide/errors.md).

## Kde nájdem svoj API token?

V nastaveniach vášho účtu na chcemfakturu.sk. Uložte ho mimo repozitára (premenná prostredia, úložisko tajomstiev) a nikdy ho nelogujte. Pozrite si [Bezpečnosť](./guide/security.md).

## Môžem klienta vyskúšať bez účtu?

Áno. [Príklady](./examples/) bežia proti simulovanému API, ktoré spustíte cez `php -S`, a [`TestTransport`](./guide/testing.md#testtransport) nepotrebuje vôbec žiadny server.

## Môžem otestovať integráciu bez volania API?

Áno: odovzdajte `TestTransport`, zaraďte odpovede do fronty a skontrolujte požiadavky, ktoré váš kód poslal. Na úrovni HTTP použite Guzzle `MockHandler`. Pozrite si [Testovanie integrácie](./guide/testing.md) a [príklad s PHPUnit](./examples/testing.md).

## Ako nastavím timeout, proxy alebo ďalšie hlavičky?

Odovzdajte `GuzzleTransport` vlastného Guzzle klienta. Predvolený klient má 10-sekundový timeout. Pozrite si [Transporty](./guide/transports.md#your-own-guzzle-client).

## Opakuje klient neúspešné požiadavky?

Nie. Pre `GET` a `DELETE` pridajte Guzzle retry middleware (jeden ukazuje [príklad s opakovaním](./examples/retries-and-timeouts.md)). `createInvoice()` neopakujte naslepo: po timeoute už faktúra môže existovať.

## Prečo sa moja cena posiela ako reťazec?

Pretože ste ju zadali ako reťazec. Klient posiela čísla presne tak, ako ich dostane, takže `"40.00"` zostane `"40.00"` a `40.0` sa pošle ako `40.0`. Ak chcete JSON číslo, odovzdajte `int` alebo `float`. Pozrite si [Hodnoty sa posielajú tak, ako ich zadáte](./guide/invoices.md#values-are-sent-as-given).

## Ako dám pevnú zľavu na celú faktúru?

API to nepodporuje, preto `Invoice::setDiscount()` vyhodí `UnsupportedDiscountException`. Pridajte položku so zápornou cenou alebo dajte pevnú zľavu na položku.

## Kde nastavím dodávateľa faktúry?

Nikde: dodávateľom je účet, ku ktorému patrí API token. `Issuer` existuje kvôli kompatibilite, nemá žiadne polia a neodosiela sa.

## Čo obsahuje odpoveď na detail?

To, čo pošle API: `getData()` je dekódovaný JSON objekt. Formát odpovedí na detail a zmazanie je predpokladaný podľa endpointu na vytvorenie a nie je overený proti živému API. Ak API vášho účtu odpovedá inak, surová odpoveď je v `getStatusCode()` a `getBody()`; prosím [založte issue](https://github.com/tomaj/invoice-client/issues).

## Prečo isOk() vráti false pri odpovedi 200?

`isOk()` potrebuje stav 2xx aj `"status": "ok"` v JSON objekte. Stav 200 s HTML stránkou, prázdnym telom alebo `"status": "error"` nie je OK; `getErrorMessage()` povie, o ktorý prípad ide. Pozrite si [Odpovede](./guide/responses.md).

## Môžem namiesto Guzzle použiť iného HTTP klienta?

Áno: implementujte `TransportInterface`. [Transporty](./guide/transports.md#writing-a-transport) obsahujú kompletný PSR-18 transport. Guzzle zostáva závislosťou balíka.

## Ktoré verzie PHP sú podporované?

PHP 8.4 alebo novšie; CI testuje 8.4, 8.5 a 8.6 a nightly 8.7.

## Používal som dev-master s menným priestorom `Invoice\`. Čo sa zmenilo?

Triedy sa presunuli do `Tomaj\InvoiceClient\`, konštruktor `InvoiceApi` namiesto poľa volieb prijíma pomenované argumenty, meny a typy zliav sú enumy a chyby API sa vracajú ako odpovede. [Návod na prechod](./upgrade.md) vymenúva každú zmenu s kódom pred a po.
