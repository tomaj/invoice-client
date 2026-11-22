<?php

declare(strict_types=1);

namespace Invoice;

class Client
{
    use ArrayReader;

    private ?string $name = null;

    private ?string $company = null;

    private ?string $identifier = null;

    private ?string $vatNumber = null;

    private ?string $taxNumber = null;

    private ?Address $address = null;

    /**
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): Client
    {
        $client = new Client();
        $name = self::readString($data, 'name');
        if ($name !== null) {
            $client->setName($name);
        }
        $company = self::readString($data, 'company');
        if ($company !== null) {
            $client->setCompany($company);
        }
        $identifier = self::readString($data, 'identifier');
        if ($identifier !== null) {
            $client->setIdentifier($identifier);
        }
        $vatNumber = self::readString($data, 'vat_number');
        if ($vatNumber !== null) {
            $client->setVatNumber($vatNumber);
        }
        $taxNumber = self::readString($data, 'tax_number');
        if ($taxNumber !== null) {
            $client->setTaxNumber($taxNumber);
        }
        $address = self::readArray($data, 'address');
        if ($address !== null) {
            $client->setAddress(Address::fromArray($address));
        }
        return $client;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Client
    {
        $this->name = $name;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): Client
    {
        $this->company = $company;
        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): Client
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber): Client
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): Client
    {
        $this->taxNumber = $taxNumber;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address = null): Client
    {
        $this->address = $address;
        return $this;
    }
}
