---
description: Vytvorte faktúru z poľa cez fromArray(), načítajte ju, zostavte jej odkazy, zmažte ju a pozrite sa na následnú 404.
---

# Príklad: životný cyklus faktúry

<ProjectSource name="invoice-lifecycle" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

Faktúra od prvej po poslednú požiadavku: zostavená cez `Invoice::fromArray()` z riadku objednávky, vytvorená, načítaná cez `getInvoice()`, s odkazmi z `htmlUrl()` a `pdfDownloadUrl()`, zmazaná cez `deleteInvoice()` a potom ešte raz načítaná a zmazaná, aby bolo vidieť, že 404 je odpoveď, nie výnimka.

## Kód {#the-code}

<<< @/../examples/invoice-lifecycle/run.php

## Výstup {#output}

<<< @/../examples/invoice-lifecycle/expected-output.txt{text}

## Na čo si všimnúť {#what-to-notice}

- **`fromArray()`** berie názvy polí z JSON, vnorené polia pre odberateľa, jeho adresu, položky a ich zľavy a menu ako kód. Jeho pravidlá pre neočakávané typy sú v [Zostavení faktúry](../guide/invoices.md#fromarray).
- **`getData()`** odpovede na detail je dekódovaný JSON presne tak, ako ho API poslalo; klient ho späť do `Invoice` nemapuje.
- **404 je odpoveď.** Po zmazaní vrátia `getInvoice()` aj `deleteInvoice()` `isOk() === false` so stavom 404 a správou z API; nič sa nevyhodí.
- **Id sa kódujú do URL.** `2026/002` sa pošle ako `2026%2F002`, takže lomka nemôže zmeniť cestu.

::: warning Formát detailu a zmazania
Simulované API odpovedá na detail `{"status": "ok", "invoice": {...}}` a na zmazanie `{"status": "ok"}`. Tieto formáty sú predpokladané, nie overené proti živému API chcemfakturu.sk; pozrite [Volania API](../guide/api.md#delete-an-invoice).
:::
