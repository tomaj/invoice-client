<?php

declare(strict_types=1);

namespace Invoice;

use Invoice\Exception\UnsupportedCurrencyException;

class Invoice
{
    use ArrayReader;

    private ?string $number = null;

    private ?string $name = null;

    private ?string $id = null;

    private ?string $created = null;

    private ?string $delivered = null;

    private ?string $due = null;

    private ?string $status = null;

    private ?string $description = null;

    private ?string $variableSymbol = null;

    private ?string $constantSymbol = null;

    private int|float|null $price = null;

    private int|float|null $priceTotal = null;

    private ?Address $shippingAddress = null;

    private ?Issuer $issuer = null;

    private ?Client $client = null;

    private ?string $currency = null;

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
        $number = self::readString($data, 'number');
        if ($number !== null) {
            $invoice->setNumber($number);
        }
        $name = self::readString($data, 'name');
        if ($name !== null) {
            $invoice->setName($name);
        }
        $id = self::readString($data, 'id');
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
        $variableSymbol = self::readString($data, 'variable_symbol');
        if ($variableSymbol !== null) {
            $invoice->setVariableSymbol($variableSymbol);
        }
        $constantSymbol = self::readString($data, 'constant_symbol');
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
            if (!is_string($currency) || !Currency::isValid($currency)) {
                throw new UnsupportedCurrencyException(sprintf("Unsupported currency '%s'", is_scalar($currency) ? $currency : get_debug_type($currency)));
            }
            $invoice->setCurrency($currency);
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): Invoice
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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): Invoice
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

    public function getVariableSymbol(): ?string
    {
        return $this->variableSymbol;
    }

    public function setVariableSymbol(?string $variableSymbol): Invoice
    {
        $this->variableSymbol = $variableSymbol;
        return $this;
    }

    public function getConstantSymbol(): ?string
    {
        return $this->constantSymbol;
    }

    public function setConstantSymbol(?string $constantSymbol): Invoice
    {
        $this->constantSymbol = $constantSymbol;
        return $this;
    }

    public function getPrice(): int|float|null
    {
        return $this->price;
    }

    public function setPrice(int|float|null $price): Invoice
    {
        $this->price = $price;
        return $this;
    }

    public function getPriceTotal(): int|float|null
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(int|float|null $priceTotal): Invoice
    {
        $this->priceTotal = $priceTotal;
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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): Invoice
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
     * @throws Exception when a flat discount is given
     */
    public function setDiscount(Discount $discount): Invoice
    {
        if ($discount->getType() === 'flat') {
            throw new Exception('Flat discount is unsupported for invoice. Add new item with negative price for flat discount.');
        }
        $this->discount = $discount;
        return $this;
    }
}
