---
description: Každé pole Invoice, InvoiceItem, Client, Address a Discount, JSON, ktorý z nich vznikne, meny, zľavy a pravidlá fromArray().
---

# Zostavenie faktúry

`Invoice` drží polia faktúry, zoznam položiek faktúry (`InvoiceItem`), odberateľa (`Client`) s adresou (`Address`), voliteľnú dodaciu adresu (`Address`) a zľavu (`Discount`). Všetky triedy sú v `Tomaj\InvoiceClient\Invoice\`. Každý setter vracia samotný objekt, takže volania môžete reťaziť, a každá trieda okrem `Discount` má konštruktor `fromArray()`, ktorý berie názvy polí z JSON.

<<< @/snippets/build-invoice.php

Výstup: telo požiadavky, ktoré `createInvoice()` pošle pre `$fromArray`:

<<< @/snippets/build-invoice.php.out{json}

[Príklad create-invoice](../examples/create-invoice.md) vyplní každé pole a ukáže, čo API dostane.

## Hodnoty sa posielajú tak, ako ich zadáte {#values-are-sent-as-given}

Klient hodnoty neprevádza, nezaokrúhľuje ani neformátuje. Čo nastavíte, to API dostane:

- **Čísla** (`price`, `price_total`, `quantity`, `vat`, `price_item`, `value` zľavy) prijímajú `int`, `float` alebo číselný reťazec. `"40.00"` sa pošle ako JSON reťazec `"40.00"`, `10` ako číslo `10`, `10.5` ako `10.5`. Nečíselný reťazec vyhodí v setteri `InvalidValueException`. `null` pole vymaže.
- **Kódy** (`id`, `number`, `variable_symbol`, `constant_symbol`, `identifier`, `vat_number`, `tax_number`, `zip`, `tel`, `fax`) prijímajú `string` alebo `int`. Ak chcete zachovať úvodné nuly, použite reťazec: `'0308'`.
- **Texty a dátumy** (`name`, `description`, `created`, `delivered`, `due`, `status`, riadky adresy, `email`) sú reťazce a posielajú sa nezmenené. Dátumy píšte vo formáte, ktorý API očakáva (`YYYY-MM-DD`); klient ich neparsuje.
- **Nenastavené polia** faktúry, položiek, odberateľa a adries sa pošlú ako `null`. `client`, `shipping_address` a `discount` sa v JSON vynechajú, ak nie sú nastavené, a rovnako zľava typu `none`.

Settery prijímajú tieto typy aj pod `declare(strict_types=1)`.

## Invoice

| Setter | Typ | Pole v JSON | Význam |
| --- | --- | --- | --- |
| `setId()` | `string\|int\|null` | `id` | vaše vlastné id faktúry, ak ho má API poznať |
| `setNumber()` | `string\|int\|null` | `number` | číslo faktúry |
| `setName()` | `?string` | `name` | názov faktúry, napr. `Invoice 2026001` |
| `setCreated()` | `?string` | `created` | dátum vystavenia |
| `setDelivered()` | `?string` | `delivered` | dátum dodania (dátum zdaniteľného plnenia) |
| `setDue()` | `?string` | `due` | dátum splatnosti |
| `setStatus()` | `?string` | `status` | stav faktúry tak, ako ho pozná API |
| `setVariableSymbol()` | `string\|int\|null` | `variable_symbol` | variabilný symbol platby |
| `setConstantSymbol()` | `string\|int\|null` | `constant_symbol` | konštantný symbol platby |
| `setDescription()` | `?string` | `description` | text na faktúre |
| `setPrice()` | `int\|float\|string\|null` | `price` | suma bez DPH |
| `setPriceTotal()` | `int\|float\|string\|null` | `price_total` | suma s DPH |
| `setCurrency()` | `?Currency` | `currency` | kód z enumu, napr. `"EUR"` |
| `addItem()` | `InvoiceItem` | `items[]` | pridá jednu položku |
| `setItems()` | `list<InvoiceItem>` | `items` | nahradí všetky položky |
| `setClient()` | `Client` | `client` | odberateľ; ak nie je nastavený, vynechá sa |
| `setShippingAddress()` | `?Address` | `shipping_address` | pri `null` sa vynechá |
| `setDiscount()` | `Discount` | `discount` | zľava na celú faktúru, len percentuálna |
| `setIssuer()` | `?Issuer` | neposiela sa | dodávateľom je účet, ku ktorému patrí API token |

Každý setter má getter s rovnakým názvom (`getNumber()`, `getItems()`, …). Kým nezavoláte žiadny setter, každý getter vracia `null`, okrem `getItems()`, ktorý vracia `[]`.

Ktoré polia API vyžaduje a ako vypočíta súčty, keď `price` a `price_total` necháte prázdne, určuje chcemfakturu.sk; klient pošle to, čo nastavíte.

## InvoiceItem

| Setter | Typ | Pole v JSON | Význam |
| --- | --- | --- | --- |
| `setDescription()` | `?string` | `description` | text riadku |
| `setQuantity()` | `int\|float\|string\|null` | `quantity` | počet jednotiek |
| `setPriceItem()` | `int\|float\|string\|null` | `price_item` | cena za jednotku |
| `setPrice()` | `int\|float\|string\|null` | `price` | cena riadku bez DPH |
| `setPriceTotal()` | `int\|float\|string\|null` | `price_total` | cena riadku s DPH |
| `setVat()` | `int\|float\|string\|null` | `vat` | sadzba DPH v percentách, napr. `20` |
| `setDiscount()` | `?Discount` | `discount` | percentuálna alebo pevná zľava na tento riadok |

JSON položky faktúry má kľúče v tomto poradí: `vat`, `quantity`, `description`, `price_item`, `price`, `price_total` a `discount`, ak je nastavená.

## Client

| Setter | Typ | Pole v JSON | Význam |
| --- | --- | --- | --- |
| `setName()` | `?string` | `name` | meno osoby |
| `setCompany()` | `?string` | `company` | názov firmy |
| `setIdentifier()` | `string\|int\|null` | `identifier` | IČO |
| `setVatNumber()` | `string\|int\|null` | `vat_number` | IČ DPH |
| `setTaxNumber()` | `string\|int\|null` | `tax_number` | DIČ |
| `setAddress()` | `?Address` | `address` | pri `null` sa vynechá |

## Address

Používa sa pre adresu odberateľa aj pre dodaciu adresu.

| Setter | Typ | Pole v JSON |
| --- | --- | --- |
| `setStreet()` | `?string` | `street` |
| `setStreet2()` | `?string` | `street2` |
| `setZip()` | `string\|int\|null` | `zip` |
| `setCity()` | `?string` | `city` |
| `setCountry()` | `?string` | `country` |
| `setState()` | `?string` | `state` |
| `setEmail()` | `?string` | `email` |
| `setTel()` | `string\|int\|null` | `tel` |
| `setFax()` | `string\|int\|null` | `fax` |

## Zľavy {#discounts}

`Discount` je nemenný hodnotový objekt: `new Discount(DiscountType $type, int|float|string $value)`.

```php
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;

$tenPercent = new Discount(DiscountType::Percent, 10);  // {"type": "percent", "value": 10}
$fifteenOff = new Discount(DiscountType::Flat, '15.00'); // {"type": "flat", "value": "15.00"}, len na položke
$none = new Discount(DiscountType::None, 0);            // vôbec sa neodošle
```

| `DiscountType` | Hodnota | Povolená na |
| --- | --- | --- |
| `None` | `none` | kdekoľvek; zľava sa v JSON vynechá |
| `Percent` | `percent` | položke aj celej faktúre |
| `Flat` | `flat` | len na položke |

- Nečíselný reťazec ako hodnota vyhodí `InvalidValueException`.
- `Invoice::setDiscount()` s pevnou zľavou vyhodí `UnsupportedDiscountException`, pretože API pevnú zľavu na celú faktúru nepodporuje. Namiesto nej pridajte položku so zápornou cenou alebo dajte zľavu na položku.

## Meny {#currencies}

`Currency` je enum s reťazcovými hodnotami, ktorý obsahuje meny akceptované API:

| Prípad | Kód |
| --- | --- |
| `Currency::EUR` | `EUR` |
| `Currency::USD` | `USD` |
| `Currency::CZK` | `CZK` |
| `Currency::GBP` | `GBP` |
| `Currency::HUF` | `HUF` |
| `Currency::PLN` | `PLN` |
| `Currency::RUB` | `RUB` |

Pre kód, ktorý máte ako reťazec, použite `Currency::from('EUR')` (pre neznámy kód vyhodí PHP `ValueError`) alebo `Currency::tryFrom($code)` (vráti `null`). `Invoice::fromArray()` prijme kód a namiesto toho vyhodí `UnsupportedCurrencyException` z balíka.

## fromArray()

`Invoice::fromArray()`, `InvoiceItem::fromArray()`, `Client::fromArray()`, `Address::fromArray()` a `Discount::fromArray()` zostavia objekty z poľa s názvami polí z JSON, napríklad z riadku vašej databázy alebo dekódovaného formulára. Vnorené polia sa čítajú pre `client` (s jeho `address`), `shipping_address`, `issuer`, `items` (zoznam polí položiek) a `discount`.

K typom sú zhovievavé, k hodnotám, ktoré nemôžu byť správne, prísne:

| Druh poľa | Prijme | Ignoruje (pole zostane nenastavené) |
| --- | --- | --- |
| texty a dátumy | reťazce; inty a floaty prevedie na reťazce | booleany, polia, `null` |
| kódy | reťazce a inty tak, ako sú; floaty prevedie na reťazce | booleany, polia, `null` |
| čísla | inty, floaty, číselné reťazce | booleany, prázdne alebo nečíselné reťazce, polia |
| vnorené objekty a `items` | polia | čokoľvek iné; prvky `items`, ktoré nie sú poľom, preskočí |

- `currency` musí byť prípad `Currency` alebo jeden z jeho kódov; čokoľvek iné (neznámy kód, int) vyhodí `UnsupportedCurrencyException`. Chýbajúca mena alebo `null` zostane nenastavená.
- `type` zľavy musí byť `none`, `percent` alebo `flat` (chýbajúci znamená `none`), inak `InvalidValueException`. Jej `value` sa prevedie na float, takže `10` sa pošle ako `10.0`.
- Pevná zľava (`discount`) na samotnej faktúre vyhodí `UnsupportedDiscountException`, rovnako ako `setDiscount()`.
- Neznáme kľúče sa ignorujú.

[Príklad invoice-lifecycle](../examples/invoice-lifecycle.md) vytvorí faktúru z poľa.

## Zakódovanie bez odoslania {#encoding-without-sending}

`Serializer::encodeInvoice(Invoice $invoice): string` vráti presne to telo JSON, ktoré pošle `createInvoice()`; hodí sa na zalogovanie toho, čo by ste poslali, v teste alebo pri ladení. Vyhodí `SerializationException`, keď faktúru nemožno zakódovať do JSON, napríklad preto, že text nie je platné UTF-8.
