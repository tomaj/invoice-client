<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Invoice;

final class Client
{
    use ArrayReader;

    private ?string $name = null;

    private ?string $company = null;

    private string|int|null $identifier = null;

    private string|int|null $vatNumber = null;

    private string|int|null $taxNumber = null;

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
        $identifier = self::readIdentifier($data, 'identifier');
        if ($identifier !== null) {
            $client->setIdentifier($identifier);
        }
        $vatNumber = self::readIdentifier($data, 'vat_number');
        if ($vatNumber !== null) {
            $client->setVatNumber($vatNumber);
        }
        $taxNumber = self::readIdentifier($data, 'tax_number');
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

    public function getIdentifier(): string|int|null
    {
        return $this->identifier;
    }

    public function setIdentifier(string|int|null $identifier): Client
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getVatNumber(): string|int|null
    {
        return $this->vatNumber;
    }

    public function setVatNumber(string|int|null $vatNumber): Client
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    public function getTaxNumber(): string|int|null
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string|int|null $taxNumber): Client
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
