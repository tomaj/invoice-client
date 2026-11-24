---
description: Objekty odpovede invoice-client - kedy je isOk() true, všetky chybové správy, getData() a ďalšie gettery CreateInvoiceResponse.
---

# Odpovede

Každé volanie `InvoiceApi` vráti objekt odpovede, nech API odpovedalo čokoľvek. Všetky implementujú `Tomaj\InvoiceClient\Response\ResponseInterface`:

| Metóda | Vracia |
| --- | --- |
| `isOk(): bool` | `true`, keď je HTTP stav 2xx **a** telo JSON obsahuje `"status": "ok"` |
| `getStatusCode(): int` | HTTP stavový kód; `0`, keď sa neodoslala žiadna požiadavka (`NullTransport` alebo `TestTransport` bez odpovede vo fronte) |
| `getErrorMessage(): ?string` | `null` pri úspechu; inak `message` z API alebo jednu zo správ nižšie |
| `getBody(): string` | surové telo odpovede, aj pri chybách; `''`, keď sa neodoslala žiadna požiadavka |
| `getData(): array` | dekódovaný JSON objekt; `[]`, keď telo nie je JSON objekt |

## Kedy je odpoveď OK? {#when-is-a-response-ok}

Musia platiť obe podmienky:

1. HTTP stav je medzi 200 a 299 a
2. telo je JSON objekt s `"status": "ok"`.

Stav 200 s `{"status": "error", ...}` je chyba a rovnako aj 500 s `{"status": "ok"}`.

## Chybové správy {#error-messages}

| Situácia | `getErrorMessage()` |
| --- | --- |
| JSON má neprázdny reťazec `message` a odpoveď nie je OK | `message`, napr. `Invalid API token` |
| Telo nie je JSON objekt: chybová HTML stránka, prázdne telo, JSON zoznam alebo `{}` | `Cannot parse response from server (HTTP 502)` |
| `"status"` nie je `"ok"` a nie je k dispozícii použiteľná `message` | `Unknown error` |
| `"status": "ok"`, ale HTTP stav nie je 2xx, bez `message` | `Unexpected HTTP status 500` |
| Neodoslala sa žiadna požiadavka | `No request was sent` |

[Príklad error-handling](../examples/error-handling.md) vyvolá každú z nich proti simulovanému API.

## getData()

`getData()` je dekódované telo, ak je to JSON objekt (asociatívne pole), inak `[]`. Je vyplnené aj pri chybách, takže si prečítate aj ďalšie polia, ktoré chybová odpoveď môže niesť:

```php
$response = $api->getInvoice('inv-1');
$data = $response->getData(); // napr. ['status' => 'ok', 'invoice' => [...]] so simulovaným API
```

Klient detail nemapuje do objektov: `getData()` je presne to, čo API poslalo.

## CreateInvoiceResponse

Vracia ju `createInvoice()`. Pridáva tri gettery, všetky vracajú `null`, pokiaľ odpoveď nie je OK:

| Metóda | Pole v JSON | Význam |
| --- | --- | --- |
| `getId(): ?string` | `id` | id novej faktúry; celočíselné id sa vráti ako reťazec |
| `getHtmlUrl(): ?string` | `html_url` | odkaz na HTML verziu |
| `getDownloadUrl(): ?string` | `download_pdf_url` | odkaz na stiahnutie PDF |

Úspešná odpoveď vyzerá takto:

```json
{
    "status": "ok",
    "id": "inv-0001",
    "html_url": "https://chcemfakturu.sk/html/inv-0001",
    "download_pdf_url": "https://chcemfakturu.sk/pdf-download/inv-0001"
}
```

Pole, ktoré chýba alebo nie je reťazec ani celé číslo, dá `null`.

## InvoiceDetailResponse

Vracia ju `getInvoice()`. Má len spoločné metódy; faktúra je v `getData()` tak, ako ju API poslalo.

## DeleteInvoiceResponse

Vracia ju `deleteInvoice()`. Má len spoločné metódy; `isOk()` povie, či sa faktúra zmazala.

::: warning Predpokladaný formát
Formát odpovedí na detail a zmazanie nie je overený proti živému API: klient predpokladá rovnakú obálku ako pri vytvorení (`"status"` a `"message"`). Ak sa pre váš účet líši, `getStatusCode()`, `getBody()` a `getData()` aj tak obsahujú surovú odpoveď. Pozrite si [Volania API](./api.md#delete-an-invoice).
:::

## Vlastné zostavenie odpovede {#building-a-response-yourself}

Každá trieda odpovede prijíma v konštruktore `?Transport\Result` (stavový kód a telo), čo sa hodí v testoch kódu, ktorý odpoveď dostáva:

```php
use Tomaj\InvoiceClient\Response\CreateInvoiceResponse;
use Tomaj\InvoiceClient\Transport\Result;

$response = new CreateInvoiceResponse(new Result(422, '{"status":"error","message":"Invalid VAT number"}'));
$response->isOk();            // false
$response->getErrorMessage(); // 'Invalid VAT number'
```
