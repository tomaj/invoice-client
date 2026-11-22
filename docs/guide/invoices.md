# Building Invoices

An `Invoice` holds the invoice fields, a list of `InvoiceItem`s, a `Client` with an `Address`, an optional shipping `Address` and a `Discount`. Every setter returns the object, so calls can be chained, and every class has a `fromArray()` constructor.

<<< @/snippets/build-invoice.php

Output (the request body that `createInvoice()` sends):

<<< @/snippets/build-invoice.php.out{json}

## Values are sent as given

- **Numbers** (`price`, `price_total`, `quantity`, `vat`, `price_item`, discount `value`) accept `int`, `float` or a numeric string. `"40.00"` stays the string `"40.00"`, `10` stays an integer. A non-numeric string throws `InvalidValueException`.
- **Codes** (`number`, `id`, `variable_symbol`, `constant_symbol`, `identifier`, `vat_number`, `tax_number`, `zip`, `tel`, `fax`) accept `string` or `int`. Use a string to keep leading zeros (`'0308'`).
- **Texts and dates** (`name`, `description`, `created`, `delivered`, `due`, `status`, address lines) are strings and are sent unchanged; dates in the format the API expects (`YYYY-MM-DD`).
- A field you do not set is sent as `null`. The client, shipping address and discount are left out when not set.

## Invoice

| Setter | JSON field |
| --- | --- |
| `setId(string\|int\|null)` | `id` |
| `setNumber(string\|int\|null)` | `number` |
| `setName(?string)` | `name` |
| `setCreated(?string)`, `setDelivered(?string)`, `setDue(?string)` | `created`, `delivered`, `due` |
| `setStatus(?string)` | `status` |
| `setVariableSymbol(string\|int\|null)`, `setConstantSymbol(string\|int\|null)` | `variable_symbol`, `constant_symbol` |
| `setDescription(?string)` | `description` |
| `setPrice(int\|float\|string\|null)`, `setPriceTotal(...)` | `price`, `price_total` |
| `setCurrency(?Currency)` | `currency` |
| `addItem(InvoiceItem)`, `setItems(list<InvoiceItem>)` | `items` |
| `setClient(Client)` | `client` |
| `setShippingAddress(?Address)` | `shipping_address` |
| `setDiscount(Discount)` | `discount` |
| `setIssuer(?Issuer)` | not sent: the issuer is the account of the API token |

## InvoiceItem

`setDescription()`, `setQuantity()`, `setPriceItem()` (unit price), `setPrice()`, `setPriceTotal()`, `setVat()` (percent) and `setDiscount()`.

## Client and Address

`Client`: `setName()`, `setCompany()`, `setIdentifier()` (IČO), `setVatNumber()` (IČ DPH), `setTaxNumber()` (DIČ), `setAddress()`.

`Address`: `setStreet()`, `setStreet2()`, `setZip()`, `setCity()`, `setCountry()`, `setState()`, `setEmail()`, `setTel()`, `setFax()`.

## Discounts

```php
new Discount(DiscountType::Percent, 10);   // 10 %
new Discount(DiscountType::Flat, '15.00'); // 15.00 off, items only
new Discount(DiscountType::None, 0);       // no discount, not sent
```

A flat discount on the whole invoice is not supported by the API: `Invoice::setDiscount()` throws `UnsupportedDiscountException`. Add an item with a negative price instead.

## Currencies

`Currency` is an enum of the currencies the API accepts: `EUR`, `USD`, `CZK`, `GBP`, `HUF`, `PLN`, `RUB`. Use `Currency::from('EUR')` for a value you have as a string.

## fromArray()

`Invoice::fromArray()` takes the JSON field names from the tables above, with nested arrays for `client` (and its `address`), `shipping_address`, `items` and `discount`:

- values of unexpected types are ignored (a boolean price, a non-numeric string for a number, a string for `items`);
- `currency` must be a `Currency` or one of its codes, otherwise `UnsupportedCurrencyException` is thrown;
- a discount `type` must be `none`, `percent` or `flat` (default `none`), otherwise `InvalidValueException`; its `value` is converted to a float.
