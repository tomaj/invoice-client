<?php

declare(strict_types=1);

namespace Invoice;

class InvoiceItem
{
    use ArrayReader;

    /** @var int|float|numeric-string|null */
    private int|float|string|null $quantity = null;

    /** @var int|float|numeric-string|null */
    private int|float|string|null $vat = null;

    /** @var int|float|numeric-string|null */
    private int|float|string|null $priceItem = null;

    /** @var int|float|numeric-string|null */
    private int|float|string|null $price = null;

    /** @var int|float|numeric-string|null */
    private int|float|string|null $priceTotal = null;

    private ?string $description = null;

    private ?Discount $discount = null;

    /**
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): InvoiceItem
    {
        $invoiceItem = new InvoiceItem();
        $quantity = self::readNumber($data, 'quantity');
        if ($quantity !== null) {
            $invoiceItem->setQuantity($quantity);
        }
        $vat = self::readNumber($data, 'vat');
        if ($vat !== null) {
            $invoiceItem->setVat($vat);
        }
        $priceItem = self::readNumber($data, 'price_item');
        if ($priceItem !== null) {
            $invoiceItem->setPriceItem($priceItem);
        }
        $price = self::readNumber($data, 'price');
        if ($price !== null) {
            $invoiceItem->setPrice($price);
        }
        $priceTotal = self::readNumber($data, 'price_total');
        if ($priceTotal !== null) {
            $invoiceItem->setPriceTotal($priceTotal);
        }
        $description = self::readString($data, 'description');
        if ($description !== null) {
            $invoiceItem->setDescription($description);
        }
        $discount = self::readArray($data, 'discount');
        if ($discount !== null) {
            $invoiceItem->setDiscount(Discount::fromArray($discount));
        }
        return $invoiceItem;
    }

    /**
     * @return int|float|numeric-string|null
     */
    public function getQuantity(): int|float|string|null
    {
        return $this->quantity;
    }

    /**
     * @param int|float|string|null $quantity a number or a numeric string, sent to the API as given
     * @throws \InvalidArgumentException for a non-numeric string
     */
    public function setQuantity(int|float|string|null $quantity): InvoiceItem
    {
        $this->quantity = self::numeric($quantity, 'quantity');
        return $this;
    }

    /**
     * @return int|float|numeric-string|null
     */
    public function getVat(): int|float|string|null
    {
        return $this->vat;
    }

    /**
     * @param int|float|string|null $vat a number or a numeric string, sent to the API as given
     * @throws \InvalidArgumentException for a non-numeric string
     */
    public function setVat(int|float|string|null $vat): InvoiceItem
    {
        $this->vat = self::numeric($vat, 'vat');
        return $this;
    }

    /**
     * @return int|float|numeric-string|null
     */
    public function getPriceItem(): int|float|string|null
    {
        return $this->priceItem;
    }

    /**
     * @param int|float|string|null $priceItem a number or a numeric string, sent to the API as given
     * @throws \InvalidArgumentException for a non-numeric string
     */
    public function setPriceItem(int|float|string|null $priceItem): InvoiceItem
    {
        $this->priceItem = self::numeric($priceItem, 'price_item');
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
     * @throws \InvalidArgumentException for a non-numeric string
     */
    public function setPrice(int|float|string|null $price): InvoiceItem
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
     * @throws \InvalidArgumentException for a non-numeric string
     */
    public function setPriceTotal(int|float|string|null $priceTotal): InvoiceItem
    {
        $this->priceTotal = self::numeric($priceTotal, 'price_total');
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): InvoiceItem
    {
        $this->description = $description;
        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): InvoiceItem
    {
        $this->discount = $discount;
        return $this;
    }
}
