<?php

namespace Invoice;

class InvoiceItem
{
    private $quantity;

    private $vat;

    private $priceItem;

    private $price;

    private $priceTotal;

//	private $discount;

    public static function fromArray($data)
    {
        $item = new InvoiceItem();
        if (isset($data['quantity'])) {
            $item->setQuantity($data['quantity']);
        }
        if (isset($data['vat'])) {
            $item->setVat($data['vat']);
        }
        if (isset($data['price_item'])) {
            $item->setPriceItem($data['price_item']);
        }
        if (isset($data['price'])) {
            $item->setPrice($data['price']);
        }
        if (isset($data['price_total'])) {
            $item->setPriceTotal($data['price_total']);
        }
        return $item;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return InvoiceItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param float $vat
     * @return InvoiceItem
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return float mixed
     */
    public function getPriceItem()
    {
        return $this->priceItem;
    }

    /**
     * @param float $priceItem
     * @return InvoiceItem
     */
    public function setPriceItem($priceItem)
    {
        $this->priceItem = $priceItem;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return InvoiceItem
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getPriceTotal()
    {
        return $this->priceTotal;
    }

    /**
     * @param float $priceTotal
     * @return InvoiceItem
     */
    public function setPriceTotal($priceTotal)
    {
        $this->priceTotal = $priceTotal;
        return $this;
    }

//    /**
//     * @return Discount
//     */
//    public function getDiscount()
//    {
//        return $this->discount;
//    }
//
//    /**
//     * @param Discount $discount
//     * @return InvoiceItem
//     */
//    public function setDiscount($discount)
//    {
//        $this->discount = $discount;
//        return $this;
//    }
}
