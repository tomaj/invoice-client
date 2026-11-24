---
description: Nainštalujte tomaj/invoice-client, vytvorte prvú faktúru cez API chcemfakturu.sk a prečítajte si odpoveď.
---

# Začíname

invoice-client je PHP klient pre fakturačné API služby [chcemfakturu.sk](https://chcemfakturu.sk): vytvára faktúry, načítava ich a maže a skladá odkazy na ich HTML a PDF verziu. Je to nezávislý open-source balík, nie oficiálny produkt chcemfakturu.sk.

## Požiadavky {#requirements}

- PHP 8.4 alebo novšie (testované sú 8.4, 8.5 a 8.6)
- `guzzlehttp/guzzle` 7.15.2+ alebo 8.0.1+ a `psr/log` 2 alebo 3, nainštalujú sa spolu s balíkom
- API token vášho účtu na chcemfakturu.sk

## Inštalácia {#installation}

```bash
composer require tomaj/invoice-client
```

::: info Pred verziou 1.0.0
Zatiaľ nie je otagovaná žiadna verzia. Kým nevyjde 1.0.0, vyžadujte `dev-master`:

```bash
composer require tomaj/invoice-client:dev-master
```

Ak ste `dev-master` používali ešte pred menným priestorom `Tomaj\InvoiceClient\`, postupujte podľa [návodu na prechod](../upgrade.md).
:::

## Vaša prvá faktúra {#your-first-invoice}

<<< @/snippets/getting-started.php

1. `new InvoiceApi($token)` vytvorí klienta API. Komunikuje s `https://chcemfakturu.sk` cez Guzzle s 10-sekundovým timeoutom; všetky argumenty popisuje [prehľad konfigurácie](./configuration.md).
2. `Invoice`, `InvoiceItem` a ďalšie triedy z `Tomaj\InvoiceClient\Invoice\` držia dáta. Každý setter vracia samotný objekt, takže volania môžete reťaziť. Pozrite si [Zostavenie faktúry](./invoices.md).
3. `createInvoice()` pošle `POST /api/v1/invoices/create` a vráti `CreateInvoiceResponse`.
4. `isOk()` je `true` len vtedy, keď API odpovedalo stavom 2xx a `"status": "ok"`. Inak `getErrorMessage()` povie prečo: neplatný token, chyba validácie ani chyba servera **nie sú** výnimky. Pozrite si [Odpovede](./responses.md).

## Čo dostanete späť {#what-you-get-back}

Bez účtu si to isté volanie vyskúšate proti simulovanému API z [príkladov](../examples/). [Príklad create-invoice](../examples/create-invoice.md) zostaví kompletnú faktúru, vytvorí ju a vypíše odpoveď, odkazy a faktúru tak, ako ju API uložilo. Jeho výstup, ktorý CI kontroluje pri každom pushi:

<<< @/../examples/create-invoice/expected-output.txt{text}

`getId()`, `getHtmlUrl()` a `getDownloadUrl()` pochádzajú z JSON odpovede API:

```json
{"status": "ok", "id": "inv-0001", "html_url": "https://…/html/inv-0001", "download_pdf_url": "https://…/pdf-download/inv-0001"}
```

`htmlUrl()`, `pdfUrl()` a `pdfDownloadUrl()` zostavia rovnaký odkaz z id, ktoré ste si uložili skôr, bez požiadavky.

## Token držte v tajnosti {#keep-the-token-secret}

Token je bearer tajomstvo: kto ho má, môže vytvárať, čítať a mazať faktúry vášho účtu. Čítajte ho z prostredia alebo z úložiska tajomstiev, nikdy ho necommitujte a ponechajte predvolený cieľ `https://`. Klient ho posiela len v hlavičke `Authorization` a nikdy ho neloguje. Viac v časti [Bezpečnosť](./security.md).

## Ďalšie kroky {#next-steps}

<LinkCards :items="[
  { title: 'Ako to funguje', text: 'Cesta požiadavky z vášho kódu do API a späť.', link: '/sk/guide/how-it-works' },
  { title: 'Zostavenie faktúry', text: 'Každé pole, položka faktúry, odberateľ, adresa, zľava a mena.', link: '/sk/guide/invoices' },
  { title: 'Spracovanie chýb', text: 'Odpovede, výnimky a čo robiť s každou z nich.', link: '/sk/guide/errors' },
  { title: 'Testovanie integrácie', text: 'Otestujte svoj kód bez siete.', link: '/sk/guide/testing' },
]" />
