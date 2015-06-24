<?php

namespace Invoice;

class Client
{
    private $name;

    private $company;

    private $identifier;

    private $vat;

    private $localVat;

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
        if (isset($data['vat'])) {
            $client->setVat($data['vat']);
        }
        if (isset($data['local_vat'])) {
            $client->setLocalVat($data['local_vat']);
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
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param string $vat
     * @return Client
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocalVat()
    {
        return $this->localVat;
    }

    /**
     * @param string $localVat
     * @return Client
     */
    public function setLocalVat($localVat)
    {
        $this->localVat = $localVat;
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
