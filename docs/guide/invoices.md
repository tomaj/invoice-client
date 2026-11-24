---
description: Every field of Invoice, InvoiceItem, Client, Address and Discount, the JSON it becomes, currencies, discounts and the fromArray() rules.
---

# Building invoices

An `Invoice` holds the invoice fields, a list of `InvoiceItem`s, a `Client` with an `Address`, an optional shipping `Address` and a `Discount`. All classes are in `Tomaj\InvoiceClient\Invoice\`. Every setter returns the object, so calls chain, and every class except `Discount` has a `fromArray()` constructor that takes the JSON field names.

<<< @/snippets/build-invoice.php

Output: the request body that `createInvoice()` sends for `$fromArray`:

<<< @/snippets/build-invoice.php.out{json}

The [create-invoice example](../examples/create-invoice.md) fills every field and shows what the API receives.

## Values are sent as given

The client does not convert, round or format values. What you set is what the API gets:

- **Numbers** (`price`, `price_total`, `quantity`, `vat`, `price_item`, discount `value`) take `int`, `float` or a numeric string. `"40.00"` is sent as the JSON string `"40.00"`, `10` as the number `10`, `10.5` as `10.5`. A non-numeric string throws `InvalidValueException` from the setter. `null` clears the field.
- **Codes** (`id`, `number`, `variable_symbol`, `constant_symbol`, `identifier`, `vat_number`, `tax_number`, `zip`, `tel`, `fax`) take `string` or `int`. Use a string to keep leading zeros: `'0308'`.
- **Texts and dates** (`name`, `description`, `created`, `delivered`, `due`, `status`, address lines, `email`) are strings and are sent unchanged. Write dates in the format the API expects (`YYYY-MM-DD`); the client does not parse them.
- **Unset fields** of the invoice, items, client and addresses are sent as `null`. `client`, `shipping_address` and `discount` are left out of the JSON when not set, and so is a discount of type `none`.

The setters accept these types under `declare(strict_types=1)` too.

## Invoice

| Setter | Type | JSON field | Meaning |
| --- | --- | --- | --- |
| `setId()` | `string\|int\|null` | `id` | your own id of the invoice, if the API should know it |
| `setNumber()` | `string\|int\|null` | `number` | invoice number |
| `setName()` | `?string` | `name` | title of the invoice, e.g. `Invoice 2026001` |
| `setCreated()` | `?string` | `created` | issue date |
| `setDelivered()` | `?string` | `delivered` | delivery date (date of taxable supply) |
| `setDue()` | `?string` | `due` | due date |
| `setStatus()` | `?string` | `status` | invoice status as the API knows it |
| `setVariableSymbol()` | `string\|int\|null` | `variable_symbol` | variable symbol of the payment |
| `setConstantSymbol()` | `string\|int\|null` | `constant_symbol` | constant symbol of the payment |
| `setDescription()` | `?string` | `description` | text on the invoice |
| `setPrice()` | `int\|float\|string\|null` | `price` | total without VAT |
| `setPriceTotal()` | `int\|float\|string\|null` | `price_total` | total with VAT |
| `setCurrency()` | `?Currency` | `currency` | the enum's code, e.g. `"EUR"` |
| `addItem()` | `InvoiceItem` | `items[]` | appends one item |
| `setItems()` | `list<InvoiceItem>` | `items` | replaces all items |
| `setClient()` | `Client` | `client` | the customer; left out when not set |
| `setShippingAddress()` | `?Address` | `shipping_address` | left out when `null` |
| `setDiscount()` | `Discount` | `discount` | discount of the whole invoice, percent only |
| `setIssuer()` | `?Issuer` | not sent | the issuer is the account of the API token |

Every setter has a getter of the same name (`getNumber()`, `getItems()`, …). Before any setter is called, every getter returns `null`, except `getItems()`, which returns `[]`.

Which fields the API requires and how it computes totals when you leave `price` and `price_total` empty is decided by chcemfakturu.sk; the client sends what you set.

## InvoiceItem

| Setter | Type | JSON field | Meaning |
| --- | --- | --- | --- |
| `setDescription()` | `?string` | `description` | text of the line |
| `setQuantity()` | `int\|float\|string\|null` | `quantity` | number of units |
| `setPriceItem()` | `int\|float\|string\|null` | `price_item` | price of one unit |
| `setPrice()` | `int\|float\|string\|null` | `price` | price of the line without VAT |
| `setPriceTotal()` | `int\|float\|string\|null` | `price_total` | price of the line with VAT |
| `setVat()` | `int\|float\|string\|null` | `vat` | VAT rate in percent, e.g. `20` |
| `setDiscount()` | `?Discount` | `discount` | percent or flat discount of this line |

The JSON of an item has the keys in this order: `vat`, `quantity`, `description`, `price_item`, `price`, `price_total`, and `discount` when set.

## Client

| Setter | Type | JSON field | Meaning |
| --- | --- | --- | --- |
| `setName()` | `?string` | `name` | person's name |
| `setCompany()` | `?string` | `company` | company name |
| `setIdentifier()` | `string\|int\|null` | `identifier` | company registration number (IČO) |
| `setVatNumber()` | `string\|int\|null` | `vat_number` | VAT number (IČ DPH) |
| `setTaxNumber()` | `string\|int\|null` | `tax_number` | tax number (DIČ) |
| `setAddress()` | `?Address` | `address` | left out when `null` |

## Address

Used for the client's address and for the shipping address.

| Setter | Type | JSON field |
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

## Discounts

`Discount` is an immutable value object: `new Discount(DiscountType $type, int|float|string $value)`.

```php
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;

$tenPercent = new Discount(DiscountType::Percent, 10);  // {"type": "percent", "value": 10}
$fifteenOff = new Discount(DiscountType::Flat, '15.00'); // {"type": "flat", "value": "15.00"}, items only
$none = new Discount(DiscountType::None, 0);            // not sent at all
```

| `DiscountType` | Value | Allowed on |
| --- | --- | --- |
| `None` | `none` | anywhere; the discount is left out of the JSON |
| `Percent` | `percent` | an item and the whole invoice |
| `Flat` | `flat` | an item only |

- A non-numeric string as the value throws `InvalidValueException`.
- `Invoice::setDiscount()` with a flat discount throws `UnsupportedDiscountException`, because the API does not support a flat discount of the whole invoice. Add an item with a negative price instead, or give the discount on an item.

## Currencies

`Currency` is a string-backed enum of the currencies the API accepts:

| Case | Code |
| --- | --- |
| `Currency::EUR` | `EUR` |
| `Currency::USD` | `USD` |
| `Currency::CZK` | `CZK` |
| `Currency::GBP` | `GBP` |
| `Currency::HUF` | `HUF` |
| `Currency::PLN` | `PLN` |
| `Currency::RUB` | `RUB` |

For a code you have as a string use `Currency::from('EUR')` (throws PHP's `ValueError` for an unknown code) or `Currency::tryFrom($code)` (returns `null`). `Invoice::fromArray()` takes the code and throws the package's `UnsupportedCurrencyException` instead.

## fromArray()

`Invoice::fromArray()`, `InvoiceItem::fromArray()`, `Client::fromArray()`, `Address::fromArray()` and `Discount::fromArray()` build the objects from an array with the JSON field names, for example a row from your database or a decoded form. Nested arrays are read for `client` (with its `address`), `shipping_address`, `issuer`, `items` (a list of item arrays) and `discount`.

They are lenient with types and strict with values that cannot be right:

| Field kind | Accepted | Ignored (field stays unset) |
| --- | --- | --- |
| texts and dates | strings; ints and floats are converted to strings | booleans, arrays, `null` |
| codes | strings and ints as given; floats are converted to strings | booleans, arrays, `null` |
| numbers | ints, floats, numeric strings | booleans, empty or non-numeric strings, arrays |
| nested objects and `items` | arrays | anything else; non-array entries of `items` are skipped |

- `currency` must be a `Currency` case or one of its codes; anything else (an unknown code, an int) throws `UnsupportedCurrencyException`. A missing or `null` currency stays unset.
- A discount `type` must be `none`, `percent` or `flat` (missing means `none`), otherwise `InvalidValueException`. Its `value` is converted to a float, so `10` is sent as `10.0`.
- A flat `discount` on the invoice itself throws `UnsupportedDiscountException`, as `setDiscount()` does.
- Unknown keys are ignored.

The [invoice-lifecycle example](../examples/invoice-lifecycle.md) creates an invoice from an array.

## Encoding without sending

`Serializer::encodeInvoice(Invoice $invoice): string` returns the exact JSON body that `createInvoice()` sends, useful for logging what you would send in a test or for debugging. It throws `SerializationException` when the invoice cannot be encoded as JSON, for example because a text is not valid UTF-8.
