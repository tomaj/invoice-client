<?php

namespace Invoice;

class Invoice
{
	/** @var string */
	private $number;

	/** @var string */
	private $name;

	/** @var string */
	private $id;

	/** @var string */
	private $created;

	/** @var string */
	private $delivered;

	/** @var string */
	private $due;

	/** @var string */
	private $status;

	/** @var Address */
	private $shippingAddress;

	/** @var string */
	private $description;

	/** @var string */
	private $variableSymbol;

	/** @var string */
	private $constantSymbol;

	/** @var Issuer */
	private $issuer;

	/** @var Client */
	private $client;

	/** @var float */
	private $price;

	/** @var float */
	private $priceTotal;

	private $items = [];

	/**
	 * @param array $data
	 * @return Invoice
	 */
	public static function fromArray(array $data)
	{
		$invoice = new Invoice();
		if (isset($data['number'])) {
			$invoice->setNumber($data['number']);
		}
		if (isset($data['name'])) {
			$invoice->setName($data['name']);
		}
		if (isset($data['id'])) {
			$invoice->setId($data['id']);
		}
		if (isset($data['created'])) {
			$invoice->setCreated($data['created']);
		}
		if (isset($data['delivered'])) {
			$invoice->setDelivered($data['delivered']);
		}
		if (isset($data['due'])) {
			$invoice->setDue($data['due']);
		}
		if (isset($data['due'])) {
			$invoice->setDue($data['due']);
		}
		if (isset($data['status'])) {
			$invoice->setStatus($data['status']);
		}
		if (isset($data['shipping_address']) && is_array($data['shipping_address'])) {
			$invoice->setShippingAddress(Address::fromArray($data['shipping_address']));
		}
		if (isset($data['description'])) {
			$invoice->setDescription($data['description']);
		}
		if (isset($data['variable_symbol'])) {
			$invoice->setVariableSymbol($data['variable_symbol']);
		}
		if (isset($data['constant_symbol'])) {
			$invoice->setConstantSymbol($data['constant_symbol']);
		}
		if (isset($data['issuer']) && is_array($data['issuer'])) {
			$invoice->setIssuer(Issuer::fromArray($data['issuer']));
		}
		if (isset($data['client']) && is_array($data['client'])) {
			$invoice->setClient(Client::fromArray($data['client']));
		}
		if (isset($data['price'])) {
			$invoice->setPrice($data['price']);
		}
		if (isset($data['price_total'])) {
			$invoice->setPriceTotal($data['price_total']);
		}
		if (isset($data['items']) && is_array($data['items'])) {
			foreach ($data['items'] as $item) {
				$invoice->addItem(InvoiceItem::fromArray($item));
			}
		}

		return $invoice;
	}

	/**
	 * @return string
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @param string $number
	 * @return Invoice
	 */
	public function setNumber($number)
	{
		$this->number = $number;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Invoice
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return Invoice
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param string $created
	 * @return Invoice
	 */
	public function setCreated($created)
	{
		$this->created = $created;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDelivered()
	{
		return $this->delivered;
	}

	/**
	 * @param string $delivered
	 * @return Invoice
	 */
	public function setDelivered($delivered)
	{
		$this->delivered = $delivered;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDue()
	{
		return $this->due;
	}

	/**
	 * @param string $due
	 * @return Invoice
	 */
	public function setDue($due)
	{
		$this->due = $due;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 * @return Invoice
	 */
	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * @return Address
	 */
	public function getShippingAddress()
	{
		return $this->shippingAddress;
	}

	/**
	 * @param Address $shippingAddress
	 * @return Invoice
	 */
	public function setShippingAddress(Address $shippingAddress = null)
	{
		$this->shippingAddress = $shippingAddress;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return Invoice
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVariableSymbol()
	{
		return $this->variableSymbol;
	}

	/**
	 * @param string $variableSymbol
	 * @return Invoice
	 */
	public function setVariableSymbol($variableSymbol)
	{
		$this->variableSymbol = $variableSymbol;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getConstantSymbol()
	{
		return $this->constantSymbol;
	}

	/**
	 * @param string $constantSymbol
	 * @return Invoice
	 */
	public function setConstantSymbol($constantSymbol)
	{
		$this->constantSymbol = $constantSymbol;
		return $this;
	}

	/**
	 * @return Issuer
	 */
	public function getIssuer()
	{
		return $this->issuer;
	}

	/**
	 * @param Issuer $issuer
	 * return Invoice
	 */
	public function setIssuer(Issuer $issuer = null)
	{
		$this->issuer = $issuer;
		return $this;
	}

	/**
	 * @return Client
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param Client $client
	 * @return Invoice
	 */
	public function setClient(Client $client)
	{
		$this->client = $client;
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
	 * @return Invoice
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
	 * @return Invoice
	 */
	public function setPriceTotal($priceTotal)
	{
		$this->priceTotal = $priceTotal;
		return $this;
	}

	/**
	 * @return array(InvoiceItem)
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param array $items
	 */
	public function setItems($items)
	{
		$this->items = $items;
	}

	/**
	 * @param InvoiceItem $invoiceItem
	 * @return $this
	 */
	public function addItem(InvoiceItem $invoiceItem)
	{
		$this->items[] = $invoiceItem;
		return $this;
	}
}

