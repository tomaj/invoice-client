---
description: Simulované API chcemfakturu.sk, proti ktorému bežia príklady - jeden PHP súbor pre vstavaný webový server, jeho endpointy, odpovede a simulované zlyhania.
---

# Simulované API

Príklady komunikujú s `examples/fake-api/server.php`, jednosúborovou simuláciou API chcemfakturu.sk pre vstavaný webový server PHP. Skopírujte si ho na end-to-end testy vlastnej aplikácie.

```bash
FAKE_API_STATE=/tmp/invoices.json php -S 127.0.0.1:8701 examples/fake-api/server.php
```

```php
$api = new InvoiceApi('test-token', target: 'http://127.0.0.1:8701');
```

## Čo odpovedá {#what-it-answers}

| Požiadavka | Odpoveď |
| --- | --- |
| akákoľvek požiadavka na `/api/…` bez `Authorization: Bearer test-token` | `401` `{"status":"error","message":"Invalid API token"}` |
| `POST /api/v1/invoices/create` | `200` `{"status":"ok","id":"inv-0001","html_url":"…/html/inv-0001","download_pdf_url":"…/pdf-download/inv-0001"}` |
| vytvorenie bez `Content-Type: application/json` | `415` `{"status":"error","message":"The request body must be sent with Content-Type: application/json"}` |
| vytvorenie s telom, ktoré nie je `{"invoice": {...}}` | `400` so správou |
| vytvorenie bez položiek | `422` `{"status":"error","message":"The invoice has no items"}` |
| vytvorenie s menou, ktorú API nepozná | `422` `{"status":"error","message":"Unsupported currency"}` |
| `GET /api/v1/invoices/detail/{id}` | `200` `{"status":"ok","invoice":{...}}` alebo `404` `Invoice not found` |
| `DELETE /api/v1/invoices/delete/{id}` | `200` `{"status":"ok"}` alebo `404` `Invoice not found` |
| nesprávna HTTP metóda | `405` `Method not allowed` |
| `GET /html/{id}`, `/pdf/{id}`, `/pdf-download/{id}` | malá HTML stránka alebo falošné PDF |

Id idú po sebe (`inv-0001`, `inv-0002`, …) a faktúry sa ukladajú do JSON súboru `FAKE_API_STATE`, takže nový súbor so stavom dá pri každom spustení rovnaké id.

::: warning Nie je to skutočné API
Odpoveď na vytvorenie zodpovedá dokumentovanému API. Odpovede na detail a zmazanie, pravidlá validácie a správy sú pre príklady vymyslené: skutočné formáty detailu a zmazania nie sú overené ([#12](https://github.com/tomaj/invoice-client/issues/12)). Simulované API používajte na testovanie toho, ako váš kód spracúva odpovede, nie na zisťovanie, čo chcemfakturu.sk prijíma.
:::

## Simulované zlyhania {#simulated-failures}

Prefix pred cestou simuluje nefunkčnú sieť alebo server:

| Prefix | Správanie | Používa ho |
| --- | --- | --- |
| `/outage/…` | `502` s HTML stránkou, ako proxy pred nefunkčným serverom | [spracovanie chýb](./error-handling.md) |
| `/flaky/…` | `503` pre prvé dve požiadavky, potom bežná odpoveď | [opakovanie a timeouty](./retries-and-timeouts.md) |
| `/slow/…` | počká 3 sekundy, potom bežná odpoveď | [opakovanie a timeouty](./retries-and-timeouts.md) |

Prefix odovzdajte ako súčasť cieľa: `new InvoiceApi('test-token', target: 'http://127.0.0.1:8701/flaky')`.

## Kód {#the-code}

<<< @/../examples/fake-api/server.php

## Pomocné skripty testov {#the-test-helpers}

`examples/ci/lib.sh` spustí server s prázdnym stavom pre každý príklad a porovná výstup; `recipe.sh` a `use-local-invoice-client.sh` nainštalujú príklad proti aktuálnemu checkoutu.

<<< @/../examples/ci/lib.sh{bash}
