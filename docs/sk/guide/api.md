---
description: Volania InvoiceApi - createInvoice, getInvoice, deleteInvoice - HTTP požiadavky, ktoré posielajú, a HTML a PDF odkazy faktúry.
---

# Volania API

```php
use Tomaj\InvoiceClient\InvoiceApi;

$api = new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'));
```

Konštruktor a jeho voľby popisuje [prehľad konfigurácie](./configuration.md).

## Volania {#calls}

| Metóda | HTTP požiadavka | Telo | Vracia |
| --- | --- | --- | --- |
| `createInvoice(Invoice $invoice)` | `POST {target}/api/v1/invoices/create` | `{"invoice": {...}}` | [`CreateInvoiceResponse`](./responses.md#createinvoiceresponse) |
| `getInvoice(string $id)` | `GET {target}/api/v1/invoices/detail/{id}` | žiadne | [`InvoiceDetailResponse`](./responses.md#invoicedetailresponse) |
| `deleteInvoice(string $id)` | `DELETE {target}/api/v1/invoices/delete/{id}` | žiadne | [`DeleteInvoiceResponse`](./responses.md#deleteinvoiceresponse) |

- Každá požiadavka nesie `Authorization: Bearer <token>`.
- `{target}` je `target` z konštruktora bez koncovej lomky, predvolene `https://chcemfakturu.sk`.
- `{id}` sa zakóduje do URL cez `rawurlencode()`: z `inv 1` je `inv%201`, z `2026/002` je `2026%2F002`.
- Volania vyhodia `TransportException`, keď nepríde žiadna HTTP odpoveď; `createInvoice()` vyhodí aj `SerializationException`, keď faktúru nemožno zakódovať. Každá HTTP odpoveď, chybová či nie, je odpoveďou. Pozrite si [Spracovanie chýb](./errors.md).

### Vytvorenie faktúry {#create-an-invoice}

```php
$response = $api->createInvoice($invoice);

if ($response->isOk()) {
    $id = $response->getId();           // uložte si ho, aby ste faktúru neskôr načítali alebo zmazali
    $pdf = $response->getDownloadUrl(); // odkaz, ktorý pošlete odberateľovi
}
```

Telo JSON zostaví `Serializer::encodeInvoice()`; všetky polia vymenúva [Zostavenie faktúry](./invoices.md).

### Načítanie faktúry {#fetch-an-invoice}

```php
$detail = $api->getInvoice($id);

if ($detail->isOk()) {
    $data = $detail->getData(); // dekódovaný JSON objekt tak, ako ho API poslalo
}
```

### Zmazanie faktúry {#delete-an-invoice}

```php
$deleted = $api->deleteInvoice($id);

if (!$deleted->isOk()) {
    echo $deleted->getStatusCode(), ' ', $deleted->getErrorMessage(), "\n"; // napr. 404 Invoice not found
}
```

::: warning Detail a zmazanie nie sú overené proti živému API
Endpoint na vytvorenie faktúry a jeho odpoveď chcemfakturu.sk dokumentuje. HTTP metódy detailu (`GET`) a zmazania (`DELETE`) a formát ich odpovedí sú **predpokladané** podľa endpointu na vytvorenie (`{"status": "ok", ...}` alebo `{"status": "error", "message": "..."}`) a nie sú overené proti živému API ([#12](https://github.com/tomaj/invoice-client/issues/12)). Klient žiadnu odpoveď neskrýva: ak API vášho účtu odpovedá inak, prečítajte `getStatusCode()`, `getBody()` a `getData()` a prosím [založte issue](https://github.com/tomaj/invoice-client/issues).
:::

[Príklad invoice-lifecycle](../examples/invoice-lifecycle.md) spustí vytvorenie, načítanie a zmazanie po sebe.

## Odkazy na faktúru {#links-of-an-invoice}

Tieto metódy len zostavia URL; nič neposielajú a nepotrebujú žiadnu odpoveď:

| Metóda | URL |
| --- | --- |
| `htmlUrl(string $id)` | `{target}/html/{id}` |
| `pdfUrl(string $id)` | `{target}/pdf/{id}` |
| `pdfDownloadUrl(string $id)` | `{target}/pdf-download/{id}` |

```php
$api->htmlUrl('inv-1');        // https://chcemfakturu.sk/html/inv-1
$api->pdfDownloadUrl('inv-1'); // https://chcemfakturu.sk/pdf-download/inv-1
```

`CreateInvoiceResponse::getHtmlUrl()` a `getDownloadUrl()` vracajú odkazy, ktoré poslalo samotné API spolu s novou faktúrou; hneď po vytvorení uprednostnite tie, pre id, ktoré máte uložené, metódy vyššie.
