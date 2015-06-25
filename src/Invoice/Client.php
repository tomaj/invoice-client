<?php

namespace Invoice;

class Client
{
    private $name;

    private $company;

    private $identifier;

    private $vatNumber;

    private $taxNumber;

    private $address;

    public static function fromArray($data)
    {
        $client = new Client();
        if (isset($data['name'])) {
            $client->setName($data['name']);
        }
        if (isset($data['company'])) {
            $client->setCompany($data['company']);
        }
        if (isset($data['vat_number'])) {
            $client->setVatNumber($data['vat_number']);
        }
        if (isset($data['tax_number'])) {
            $client->setTaxNumber($data['tax_number']);
        }
        if (isset($data['address'])) {
            $client->setAddress(Address::fromArray($data['address']));
        }
        return $client;
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
     * @return Client
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return Client
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return Client
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     * @return Client
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    /**
     * @param string $taxNumber
     * @return Client
     */
    public function setTaxNumber($taxNumber)
    {
        $this->taxNumber = $taxNumber;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return Client
     */
    public function setAddress(Address $address = null)
    {
        $this->address = $address;
        return $this;
    }
}
