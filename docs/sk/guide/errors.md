---
description: Ktoré zlyhania invoice-client sú odpovede a ktoré výnimky, každá trieda výnimky s tým, kedy sa vyhadzuje, a kompletný vzor spracovania chýb.
---

# Spracovanie chýb

Dva druhy zlyhaní, dva spôsoby spracovania:

| Druh | Príklady | Ako ho uvidíte |
| --- | --- | --- |
| API odpovedalo chybou | zlý token (401), chyba validácie (422), neznáma faktúra (404), chyba servera (5xx), chybová HTML stránka z proxy | odpoveď s `isOk() === false` |
| Odpoveď neprišla alebo je zlý vstup | zlyhanie DNS, odmietnuté spojenie, timeout, chyba TLS; nečíselná cena, nepodporovaná mena, neplatné UTF-8 | výnimka |

## Vzor {#the-pattern}

<<< @/snippets/error-handling.php

- `TransportException` zachyťte samostatne, ak chcete požiadavku zopakovať neskôr alebo upozorniť na výpadok.
- `InvoiceClientException` je rozhranie, ktoré implementuje každá výnimka balíka, takže jeden `catch` pokryje zvyšok.
- Pri každej odpovedi skontrolujte `isOk()`. Logujte `getStatusCode()`, `getErrorMessage()` a pre tiket na podporu `getBody()`. Telo požiadavky nelogujte: obsahuje osobné údaje.

## Výnimky {#exceptions}

Všetky sú v `Tomaj\InvoiceClient\Exception\` a implementujú `InvoiceClientException` (ktoré rozširuje `Throwable`).

| Výnimka | Rozširuje | Vyhadzuje ju | Kedy |
| --- | --- | --- | --- |
| `TransportException` | `RuntimeException` | `createInvoice()`, `getInvoice()`, `deleteInvoice()` (z transportu) | žiadna HTTP odpoveď: zlyhanie DNS, odmietnuté spojenie, timeout, chyba TLS, priveľa presmerovaní. Správa je `<METHOD> <URL> failed: <reason>`; výnimka z Guzzle je v `getPrevious()` |
| `SerializationException` | `RuntimeException` | `createInvoice()`, `Serializer::encodeInvoice()` | faktúru nemožno zakódovať do JSON, napr. text nie je platné UTF-8. `JsonException` je v `getPrevious()`. Nič sa neodošle |
| `InvalidValueException` | `InvalidArgumentException` | `new InvoiceApi()`; číselné settery; `new Discount()`; `Discount::fromArray()` | `target` bez `http://` alebo `https://`; nečíselný reťazec namiesto čísla; neznámy typ zľavy |
| `UnsupportedCurrencyException` | `InvalidArgumentException` | `Invoice::fromArray()` | `currency`, ktorá nie je prípadom ani kódom `Currency` |
| `UnsupportedDiscountException` | `InvalidArgumentException` | `Invoice::setDiscount()`, `Invoice::fromArray()` | pevná zľava na celú faktúru |

Správy, ktoré píše balík (sú po anglicky):

| Výnimka | Správa |
| --- | --- |
| `InvalidValueException` | `Target must start with http:// or https://, 'chcemfakturu.sk' given` |
| `InvalidValueException` | `price_item must be a number or a numeric string, 'forty' given` (názov poľa daného settera: `price`, `price_total`, `quantity`, `vat`, `price_item`, `value`) |
| `InvalidValueException` | `Unknown discount type 'gift'` |
| `UnsupportedCurrencyException` | `Unsupported currency 'BTC'` |
| `UnsupportedDiscountException` | `Flat discount is unsupported for invoice. Add new item with negative price for flat discount.` |
| `SerializationException` | `Cannot encode the invoice as JSON: Malformed UTF-8 characters, possibly incorrectly encoded` |
| `TransportException` | `POST https://chcemfakturu.sk/api/v1/invoices/create failed: cURL error 28: …` |

Samotné PHP vyhodí `TypeError` pri argumente zlého typu, napríklad pri loggeri, ktorý nie je `Psr\Log\LoggerInterface`, a `ValueError` z `Currency::from()` pri neznámom kóde.

## Chybové odpovede {#error-responses}

Keď API odpovie, dostanete odpoveď aj pri stavoch 4xx a 5xx. [Odpovede](./responses.md#error-messages) vymenúvajú každú správu, ktorú klient vytvorí, keď chýba správa z API alebo telo nemožno spracovať.

```php
$response = $api->createInvoice($invoice);

match (true) {
    $response->isOk() => null,
    $response->getStatusCode() === 401 => throw new RuntimeException('Check the chcemfakturu.sk API token'),
    $response->getStatusCode() >= 500, $response->getStatusCode() === 0 => throw new RuntimeException('Invoicing is down, try again later'),
    default => throw new RuntimeException('Invoice rejected: ' . $response->getErrorMessage()),
};
```

## Opakovanie {#retrying}

Klient nikdy neopakuje. Či je opakovanie bezpečné, závisí od volania:

- `getInvoice()` a `deleteInvoice()` môžete po `TransportException` alebo 502/503/504 zopakovať.
- `createInvoice()` by ste **nemali** opakovať naslepo: po timeoute už faktúra môže existovať a opakovanie by vytvorilo duplikát. Najprv to overte (napríklad podľa vlastného `number` alebo `variable_symbol` vo vašich záznamoch), alebo nechajte rozhodnúť človeka.

[Príklad retries-and-timeouts](../examples/retries-and-timeouts.md) pridá Guzzle retry middleware, ktorý opakuje len `GET` a `DELETE`.

## Všetky prípady v akcii {#see-every-case-run}

[Príklad error-handling](../examples/error-handling.md) vyvolá každú chybovú odpoveď a každú výnimku uvedenú vyššie proti simulovanému API a vypíše, čo dostanete.
