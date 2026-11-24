---
description: Referencia API tomaj/invoice-client - každá verejná trieda, metóda, konštanta, prípad enumu a výnimka, podľa menného priestoru.
---

# Referencia API

Koreňový menný priestor je `Tomaj\InvoiceClient`. Všetky triedy sú `final`; klienta API rozšírite implementáciou `Transport\TransportInterface`. Každý súbor deklaruje `strict_types=1`.

| Menný priestor | Obsah |
| --- | --- |
| [`Tomaj\InvoiceClient`](#invoiceapi) | `InvoiceApi`, `Serializer` |
| [`…\Invoice`](#invoice) | `Invoice`, `InvoiceItem`, `Client`, `Address`, `Discount`, `Issuer`, `Currency`, `DiscountType` |
| [`…\Response`](#response) | `ResponseInterface`, `CreateInvoiceResponse`, `InvoiceDetailResponse`, `DeleteInvoiceResponse` |
| [`…\Transport`](#transport) | `TransportInterface`, `GuzzleTransport`, `TestTransport`, `NullTransport`, `Result`, `HttpMethod` |
| [`…\Exception`](#exception) | `InvoiceClientException` a päť výnimiek |

## InvoiceApi

`final readonly class Tomaj\InvoiceClient\InvoiceApi`: klient API.

| Člen | Popis |
| --- | --- |
| `const string DEFAULT_TARGET = 'https://chcemfakturu.sk'` | predvolená základná URL |
| `__construct(string $apiToken, string $target = self::DEFAULT_TARGET, ?TransportInterface $transport = null, ?LoggerInterface $logger = null)` | vyhodí `InvalidValueException`, keď `$target` nezačína `http://` ani `https://`; odstráni koncovú `/`; predvolene použije `GuzzleTransport` a `NullLogger`. `$apiToken` je `#[\SensitiveParameter]` |
| `createInvoice(Invoice $invoice): CreateInvoiceResponse` | `POST {target}/api/v1/invoices/create`; vyhadzuje `SerializationException`, `TransportException` |
| `getInvoice(string $id): InvoiceDetailResponse` | `GET {target}/api/v1/invoices/detail/{id}`; vyhadzuje `TransportException` |
| `deleteInvoice(string $id): DeleteInvoiceResponse` | `DELETE {target}/api/v1/invoices/delete/{id}`; vyhadzuje `TransportException` |
| `htmlUrl(string $id): string` | `{target}/html/{id}`, bez požiadavky |
| `pdfUrl(string $id): string` | `{target}/pdf/{id}`, bez požiadavky |
| `pdfDownloadUrl(string $id): string` | `{target}/pdf-download/{id}`, bez požiadavky |

Id sa kódujú do URL cez `rawurlencode()`. Príručka: [Volania API](../guide/api.md), [Konfigurácia](../guide/configuration.md).

## Serializer

`final class Tomaj\InvoiceClient\Serializer`

| Metóda | Popis |
| --- | --- |
| `encodeInvoice(Invoice $invoice): string` | telo požiadavky JSON `{"invoice": {...}}` pre `createInvoice()`; vyhadzuje `SerializationException` (s `JsonException` ako previous) |

## Invoice

Menný priestor `Tomaj\InvoiceClient\Invoice`. Význam polí a ich názvy v JSON: [Zostavenie faktúry](../guide/invoices.md).

### Invoice

`final class Invoice`. Každý setter vracia `Invoice`.

| Getter | Setter | Typ |
| --- | --- | --- |
| `getId()` | `setId()` | `string\|int\|null` |
| `getNumber()` | `setNumber()` | `string\|int\|null` |
| `getName()` | `setName()` | `?string` |
| `getCreated()` | `setCreated()` | `?string` |
| `getDelivered()` | `setDelivered()` | `?string` |
| `getDue()` | `setDue()` | `?string` |
| `getStatus()` | `setStatus()` | `?string` |
| `getDescription()` | `setDescription()` | `?string` |
| `getVariableSymbol()` | `setVariableSymbol()` | `string\|int\|null` |
| `getConstantSymbol()` | `setConstantSymbol()` | `string\|int\|null` |
| `getPrice()` | `setPrice()` | get `int\|float\|numeric-string\|null`, set `int\|float\|string\|null` (pre nečíselný reťazec vyhodí `InvalidValueException`) |
| `getPriceTotal()` | `setPriceTotal()` | ako `price` |
| `getCurrency()` | `setCurrency()` | `?Currency` |
| `getClient()` | `setClient(Client $client)` | get `?Client` |
| `getShippingAddress()` | `setShippingAddress(?Address $address = null)` | `?Address` |
| `getIssuer()` | `setIssuer(?Issuer $issuer = null)` | `?Issuer` |
| `getDiscount()` | `setDiscount(Discount $discount)` | get `?Discount`; set vyhodí `UnsupportedDiscountException` pre `DiscountType::Flat` |
| `getItems(): list<InvoiceItem>` | `setItems(list<InvoiceItem> $items)`, `addItem(InvoiceItem $item)` | |

`static fromArray(array $data): Invoice` vyhadzuje `UnsupportedCurrencyException`, `InvalidValueException` (typ zľavy), `UnsupportedDiscountException` (pevná zľava na faktúru).

### InvoiceItem

`final class InvoiceItem`. Každý setter vracia `InvoiceItem`; číselné settery prijímajú `int|float|string|null` a pre nečíselný reťazec vyhodia `InvalidValueException`.

| Getter | Setter | Typ |
| --- | --- | --- |
| `getQuantity()` | `setQuantity()` | číselný |
| `getVat()` | `setVat()` | číselný |
| `getPriceItem()` | `setPriceItem()` | číselný |
| `getPrice()` | `setPrice()` | číselný |
| `getPriceTotal()` | `setPriceTotal()` | číselný |
| `getDescription()` | `setDescription()` | `?string` |
| `getDiscount()` | `setDiscount(?Discount $discount)` | `?Discount` |

`static fromArray(array $data): InvoiceItem` vyhodí `InvalidValueException` pre neznámy typ zľavy.

### Client

`final class Client`. Každý setter vracia `Client`.

| Getter | Setter | Typ |
| --- | --- | --- |
| `getName()` | `setName()` | `?string` |
| `getCompany()` | `setCompany()` | `?string` |
| `getIdentifier()` | `setIdentifier()` | `string\|int\|null` |
| `getVatNumber()` | `setVatNumber()` | `string\|int\|null` |
| `getTaxNumber()` | `setTaxNumber()` | `string\|int\|null` |
| `getAddress()` | `setAddress(?Address $address = null)` | `?Address` |

`static fromArray(array $data): Client`

### Address

`final class Address`. Každý setter vracia `Address`.

| Getter | Setter | Typ |
| --- | --- | --- |
| `getStreet()` | `setStreet()` | `?string` |
| `getStreet2()` | `setStreet2()` | `?string` |
| `getZip()` | `setZip()` | `string\|int\|null` |
| `getCity()` | `setCity()` | `?string` |
| `getCountry()` | `setCountry()` | `?string` |
| `getState()` | `setState()` | `?string` |
| `getEmail()` | `setEmail()` | `?string` |
| `getTel()` | `setTel()` | `string\|int\|null` |
| `getFax()` | `setFax()` | `string\|int\|null` |

`static fromArray(array $data): Address`

### Discount

`final readonly class Discount`: nemenný hodnotový objekt.

| Člen | Popis |
| --- | --- |
| `__construct(DiscountType $type, int\|float\|string $value)` | pre nečíselný reťazec ako hodnotu vyhodí `InvalidValueException` |
| `static fromArray(array $data): Discount` | číta `type` (predvolene `none`; pre neznámy vyhodí `InvalidValueException`) a `value` (prevedie na `float`, predvolene `0`) |
| `getType(): DiscountType` | |
| `getValue(): int\|float\|numeric-string` | tak, ako bola zadaná |

### Issuer

`final class Issuer`: nemá žiadne polia a neodosiela sa; dodávateľom je účet, ku ktorému patrí API token. `static fromArray(array $data): Issuer` `$data` ignoruje.

### Currency

`enum Currency: string`: `EUR`, `USD`, `CZK`, `GBP`, `HUF`, `PLN`, `RUB` (hodnotou každého prípadu je jeho kód). Pre kód v reťazci použite `Currency::from()` / `tryFrom()`.

### DiscountType

`enum DiscountType: string`

| Prípad | Hodnota | Význam |
| --- | --- | --- |
| `None` | `none` | bez zľavy; neodosiela sa |
| `Percent` | `percent` | hodnota je percento |
| `Flat` | `flat` | hodnota je suma; len na položkách |

## Response

Menný priestor `Tomaj\InvoiceClient\Response`. Príručka: [Odpovede](../guide/responses.md).

### ResponseInterface

| Metóda | Popis |
| --- | --- |
| `isOk(): bool` | HTTP 2xx a `"status": "ok"` |
| `getStatusCode(): int` | HTTP stav, `0`, keď sa neodoslala žiadna požiadavka |
| `getErrorMessage(): ?string` | `null` pri úspechu |
| `getBody(): string` | surové telo |
| `getData(): array` | dekódovaný JSON objekt, `[]`, keď telo nie je JSON objekt |

### CreateInvoiceResponse

`final class CreateInvoiceResponse implements ResponseInterface`

| Metóda | Popis |
| --- | --- |
| `__construct(?Result $result)` | spracuje výsledok; `null` znamená, že sa neodoslala žiadna požiadavka |
| `getId(): ?string` | `id` novej faktúry, `null`, pokiaľ odpoveď nie je OK |
| `getHtmlUrl(): ?string` | `html_url`, `null`, pokiaľ odpoveď nie je OK |
| `getDownloadUrl(): ?string` | `download_pdf_url`, `null`, pokiaľ odpoveď nie je OK |

### InvoiceDetailResponse

`final class InvoiceDetailResponse implements ResponseInterface`: `__construct(?Result $result)`; faktúra je v `getData()`.

### DeleteInvoiceResponse

`final class DeleteInvoiceResponse implements ResponseInterface`: `__construct(?Result $result)`; `isOk()` povie, či sa faktúra zmazala.

## Transport

Menný priestor `Tomaj\InvoiceClient\Transport`. Príručka: [Transporty](../guide/transports.md), [Testovanie](../guide/testing.md).

### TransportInterface

| Metóda | Popis |
| --- | --- |
| `call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result` | pošle jednu požiadavku s `Authorization: Bearer <token>` a `$payload` ako telom; vráti každú HTTP odpoveď (vrátane 4xx a 5xx), `null`, keď sa nič neodošle; vyhodí `TransportException`, keď žiadna odpoveď nepríde |

### GuzzleTransport

`final readonly class GuzzleTransport implements TransportInterface`

| Člen | Popis |
| --- | --- |
| `const int TIMEOUT = 10` | timeout predvoleného klienta v sekundách |
| `__construct(?GuzzleHttp\ClientInterface $client = null)` | váš klient alebo nový s `timeout` = `TIMEOUT` |
| `call(…): Result` | pošle `Authorization: Bearer <token>` a pri tele `Content-Type: application/json`; nikdy nevráti `null` |

### TestTransport

`final class TestTransport implements TransportInterface`

| Metóda | Popis |
| --- | --- |
| `addResponse(Result $response): self` | zaradí do fronty odpoveď na ďalšie volanie |
| `getRequests(): list<array{method: HttpMethod, url: string, apiToken: string, payload: ?string}>` | všetky zaznamenané požiadavky |
| `getLastRequest(): ?array` | posledná požiadavka alebo `null` |
| `call(…): ?Result` | zaznamená požiadavku, vráti ďalšiu odpoveď z fronty alebo `null` |

### NullTransport

`final class NullTransport implements TransportInterface`: `call(…)` vracia `null`.

### Result

`final readonly class Result`

| Metóda | Popis |
| --- | --- |
| `__construct(int $code, string $body)` | |
| `getCode(): int` | HTTP stavový kód |
| `getBody(): string` | telo odpovede |

### HttpMethod

`enum HttpMethod: string`: `Get` (`GET`), `Post` (`POST`), `Delete` (`DELETE`).

## Exception

Menný priestor `Tomaj\InvoiceClient\Exception`. Príručka: [Spracovanie chýb](../guide/errors.md).

| Typ | Rozširuje | Vyhadzuje sa, keď |
| --- | --- | --- |
| `interface InvoiceClientException` | `Throwable` | implementuje ho každá výnimka nižšie |
| `TransportException` | `RuntimeException` | neprišla žiadna HTTP odpoveď |
| `SerializationException` | `RuntimeException` | faktúru nemožno zakódovať do JSON |
| `InvalidValueException` | `InvalidArgumentException` | neplatný cieľ, nečíselná hodnota čísla, neznámy typ zľavy |
| `UnsupportedCurrencyException` | `InvalidArgumentException` | neznáma mena v `Invoice::fromArray()` |
| `UnsupportedDiscountException` | `InvalidArgumentException` | pevná zľava na celú faktúru |

## Interné {#internal}

`Invoice\ArrayReader` a `Response\ResponseTrait` sú traity označené `@internal`: implementujú `fromArray()` a spracovanie odpovede a nie sú súčasťou verejného API.
