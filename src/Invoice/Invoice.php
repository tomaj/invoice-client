<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Invoice;

use Tomaj\InvoiceClient\Exception\InvalidValueException;
use Tomaj\InvoiceClient\Exception\UnsupportedCurrencyException;
use Tomaj\InvoiceClient\Exception\UnsupportedDiscountException;

final class Invoice
{
    use ArrayReader;

    private string|int|null $number = null;

    private ?string $name = null;

    private string|int|null $id = null;

    private ?string $created = null;

    private ?string $delivered = null;

    private ?string $due = null;

    private ?string $status = null;

    private ?string $description = null;

    private string|int|null $variableSymbol = null;

    private string|int|null $constantSymbol = null;

    /** @var int|float|numeric-string|null */
    private int|float|string|null $price = null;

    /** @var int|float|numeric-string|null */
    private int|float|string|null $priceTotal = null;

    private ?Address $shippingAddress = null;

    private ?Issuer $issuer = null;

    private ?Client $client = null;

    private ?Currency $currency = null;

    /** @var list<InvoiceItem> */
    private array $items = [];

    private ?Discount $discount = null;

    /**
     * @param array<mixed> $data
     * @throws UnsupportedCurrencyException
     */
    public static function fromArray(array $data): Invoice
    {
        $invoice = new Invoice();
        $number = self::readIdentifier($data, 'number');
        if ($number !== null) {
            $invoice->setNumber($number);
        }
        $name = self::readString($data, 'name');
        if ($name !== null) {
            $invoice->setName($name);
        }
        $id = self::readIdentifier($data, 'id');
        if ($id !== null) {
            $invoice->setId($id);
        }
        $created = self::readString($data, 'created');
        if ($created !== null) {
            $invoice->setCreated($created);
        }
        $delivered = self::readString($data, 'delivered');
        if ($delivered !== null) {
            $invoice->setDelivered($delivered);
        }
        $due = self::readString($data, 'due');
        if ($due !== null) {
            $invoice->setDue($due);
        }
        $status = self::readString($data, 'status');
        if ($status !== null) {
            $invoice->setStatus($status);
        }
        $description = self::readString($data, 'description');
        if ($description !== null) {
            $invoice->setDescription($description);
        }
        $variableSymbol = self::readIdentifier($data, 'variable_symbol');
        if ($variableSymbol !== null) {
            $invoice->setVariableSymbol($variableSymbol);
        }
        $constantSymbol = self::readIdentifier($data, 'constant_symbol');
        if ($constantSymbol !== null) {
            $invoice->setConstantSymbol($constantSymbol);
        }
        $price = self::readNumber($data, 'price');
        if ($price !== null) {
            $invoice->setPrice($price);
        }
        $priceTotal = self::readNumber($data, 'price_total');
        if ($priceTotal !== null) {
            $invoice->setPriceTotal($priceTotal);
        }
        $shippingAddress = self::readArray($data, 'shipping_address');
        if ($shippingAddress !== null) {
            $invoice->setShippingAddress(Address::fromArray($shippingAddress));
        }
        $issuer = self::readArray($data, 'issuer');
        if ($issuer !== null) {
            $invoice->setIssuer(Issuer::fromArray($issuer));
        }
        $client = self::readArray($data, 'client');
        if ($client !== null) {
            $invoice->setClient(Client::fromArray($client));
        }
        if (isset($data['currency'])) {
            $currency = $data['currency'];
            $invoice->setCurrency(
                $currency instanceof Currency ? $currency : (is_string($currency) ? Currency::tryFrom($currency) : null)
                    ?? throw new UnsupportedCurrencyException(sprintf("Unsupported currency '%s'", is_string($currency) || is_int($currency) ? $currency : get_debug_type($currency))),
            );
        }
        foreach (self::readArray($data, 'items') ?? [] as $item) {
            if (is_array($item)) {
                $invoice->addItem(InvoiceItem::fromArray($item));
            }
        }
        $discount = self::readArray($data, 'discount');
        if ($discount !== null) {
            $invoice->setDiscount(Discount::fromArray($discount));
        }
        return $invoice;
    }

    public function getNumber(): string|int|null
    {
        return $this->number;
    }

    public function setNumber(string|int|null $number): Invoice
    {
        $this->number = $number;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Invoice
    {
        $this->name = $name;
        return $this;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setId(string|int|null $id): Invoice
    {
        $this->id = $id;
        return $this;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(?string $created): Invoice
    {
        $this->created = $created;
        return $this;
    }

    public function getDelivered(): ?string
    {
        return $this->delivered;
    }

    public function setDelivered(?string $delivered): Invoice
    {
        $this->delivered = $delivered;
        return $this;
    }

    public function getDue(): ?string
    {
        return $this->due;
    }

    public function setDue(?string $due): Invoice
    {
        $this->due = $due;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): Invoice
    {
        $this->status = $status;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Invoice
    {
        $this->description = $description;
        return $this;
    }

    public function getVariableSymbol(): string|int|null
    {
        return $this->variableSymbol;
    }

    public function setVariableSymbol(string|int|null $variableSymbol): Invoice
    {
        $this->variableSymbol = $variableSymbol;
        return $this;
    }

    public function getConstantSymbol(): string|int|null
    {
        return $this->constantSymbol;
    }

    public function setConstantSymbol(string|int|null $constantSymbol): Invoice
    {
        $this->constantSymbol = $constantSymbol;
        return $this;
    }

    /**
     * @return int|float|numeric-string|null
     */
    public function getPrice(): int|float|string|null
    {
        return $this->price;
    }

    /**
     * @param int|float|string|null $price a number or a numeric string, sent to the API as given
     * @throws InvalidValueException for a non-numeric string
     */
    public function setPrice(int|float|string|null $price): Invoice
    {
        $this->price = self::numeric($price, 'price');
        return $this;
    }

    /**
     * @return int|float|numeric-string|null
     */
    public function getPriceTotal(): int|float|string|null
    {
        return $this->priceTotal;
    }

    /**
     * @param int|float|string|null $priceTotal a number or a numeric string, sent to the API as given
     * @throws InvalidValueException for a non-numeric string
     */
    public function setPriceTotal(int|float|string|null $priceTotal): Invoice
    {
        $this->priceTotal = self::numeric($priceTotal, 'price_total');
        return $this;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?Address $shippingAddress = null): Invoice
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    public function getIssuer(): ?Issuer
    {
        return $this->issuer;
    }

    public function setIssuer(?Issuer $issuer = null): Invoice
    {
        $this->issuer = $issuer;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): Invoice
    {
        $this->client = $client;
        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): Invoice
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return list<InvoiceItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param list<InvoiceItem> $items
     */
    public function setItems(array $items): Invoice
    {
        $this->items = $items;
        return $this;
    }

    public function addItem(InvoiceItem $invoiceItem): Invoice
    {
        $this->items[] = $invoiceItem;
        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    /**
     * @throws UnsupportedDiscountException for a flat discount: add an item with a negative price instead
     */
    public function setDiscount(Discount $discount): Invoice
    {
        if ($discount->getType() === DiscountType::Flat) {
            throw new UnsupportedDiscountException('Flat discount is unsupported for invoice. Add new item with negative price for flat discount.');
        }
        $this->discount = $discount;
        return $this;
    }
}
