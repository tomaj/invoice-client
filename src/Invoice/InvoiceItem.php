<?php

declare(strict_types=1);

namespace Invoice;

class InvoiceItem
{
    use ArrayReader;

    private int|float|null $quantity = null;

    private int|float|null $vat = null;

    private int|float|null $priceItem = null;

    private int|float|null $price = null;

    private int|float|null $priceTotal = null;

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

    public function getQuantity(): int|float|null
    {
        return $this->quantity;
    }

    public function setQuantity(int|float|null $quantity): InvoiceItem
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getVat(): int|float|null
    {
        return $this->vat;
    }

    public function setVat(int|float|null $vat): InvoiceItem
    {
        $this->vat = $vat;
        return $this;
    }

    public function getPriceItem(): int|float|null
    {
        return $this->priceItem;
    }

    public function setPriceItem(int|float|null $priceItem): InvoiceItem
    {
        $this->priceItem = $priceItem;
        return $this;
    }

    public function getPrice(): int|float|null
    {
        return $this->price;
    }

    public function setPrice(int|float|null $price): InvoiceItem
    {
        $this->price = $price;
        return $this;
    }

    public function getPriceTotal(): int|float|null
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(int|float|null $priceTotal): InvoiceItem
    {
        $this->priceTotal = $priceTotal;
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
