---
description: The calls of InvoiceApi - createInvoice, getInvoice, deleteInvoice - the HTTP requests they send, and the HTML and PDF links of an invoice.
---

# API calls

```php
use Tomaj\InvoiceClient\InvoiceApi;

$api = new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'));
```

The constructor and its options are in the [configuration reference](./configuration.md).

## Calls

| Method | HTTP request | Body | Returns |
| --- | --- | --- | --- |
| `createInvoice(Invoice $invoice)` | `POST {target}/api/v1/invoices/create` | `{"invoice": {...}}` | [`CreateInvoiceResponse`](./responses.md#createinvoiceresponse) |
| `getInvoice(string $id)` | `GET {target}/api/v1/invoices/detail/{id}` | none | [`InvoiceDetailResponse`](./responses.md#invoicedetailresponse) |
| `deleteInvoice(string $id)` | `DELETE {target}/api/v1/invoices/delete/{id}` | none | [`DeleteInvoiceResponse`](./responses.md#deleteinvoiceresponse) |

- Every request carries `Authorization: Bearer <token>`.
- `{target}` is the constructor's `target` without a trailing slash, `https://chcemfakturu.sk` by default.
- `{id}` is URL-encoded with `rawurlencode()`: `inv 1` becomes `inv%201`, `2026/002` becomes `2026%2F002`.
- The calls throw `TransportException` when no HTTP response arrives; `createInvoice()` also throws `SerializationException` when the invoice cannot be encoded. Every HTTP answer, error or not, is a response. See [Error handling](./errors.md).

### Create an invoice

```php
$response = $api->createInvoice($invoice);

if ($response->isOk()) {
    $id = $response->getId();           // keep it to fetch or delete the invoice later
    $pdf = $response->getDownloadUrl(); // link to send to your customer
}
```

The JSON body is built by `Serializer::encodeInvoice()`; [Building invoices](./invoices.md) lists every field.

### Fetch an invoice

```php
$detail = $api->getInvoice($id);

if ($detail->isOk()) {
    $data = $detail->getData(); // the decoded JSON object, as the API sent it
}
```

### Delete an invoice

```php
$deleted = $api->deleteInvoice($id);

if (!$deleted->isOk()) {
    echo $deleted->getStatusCode(), ' ', $deleted->getErrorMessage(), "\n"; // e.g. 404 Invoice not found
}
```

::: warning Detail and delete are not verified against the live API
The create endpoint and its answer are documented by chcemfakturu.sk. The HTTP methods of detail (`GET`) and delete (`DELETE`) and the format of their answers are **assumed** to follow the create endpoint (`{"status": "ok", ...}` or `{"status": "error", "message": "..."}`) and are not verified against the live API ([#12](https://github.com/tomaj/invoice-client/issues/12)). The client never hides an answer: if your account's API responds differently, read `getStatusCode()`, `getBody()` and `getData()`, and please [open an issue](https://github.com/tomaj/invoice-client/issues).
:::

The [invoice-lifecycle example](../examples/invoice-lifecycle.md) runs create, fetch and delete one after another.

## Links of an invoice

These methods only build a URL; they send nothing and need no response:

| Method | URL |
| --- | --- |
| `htmlUrl(string $id)` | `{target}/html/{id}` |
| `pdfUrl(string $id)` | `{target}/pdf/{id}` |
| `pdfDownloadUrl(string $id)` | `{target}/pdf-download/{id}` |

```php
$api->htmlUrl('inv-1');        // https://chcemfakturu.sk/html/inv-1
$api->pdfDownloadUrl('inv-1'); // https://chcemfakturu.sk/pdf-download/inv-1
```

`CreateInvoiceResponse::getHtmlUrl()` and `getDownloadUrl()` return the links the API itself sent with the new invoice; prefer them right after creating it, and the methods above for an id you stored.
