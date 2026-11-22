<?php

declare(strict_types=1);

namespace Invoice;

class Address
{
    use ArrayReader;

    private ?string $street = null;

    private ?string $street2 = null;

    private string|int|null $zip = null;

    private ?string $email = null;

    private string|int|null $tel = null;

    private string|int|null $fax = null;

    private ?string $city = null;

    private ?string $country = null;

    private ?string $state = null;

    /**
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): Address
    {
        $address = new Address();
        $street = self::readString($data, 'street');
        if ($street !== null) {
            $address->setStreet($street);
        }
        $street2 = self::readString($data, 'street2');
        if ($street2 !== null) {
            $address->setStreet2($street2);
        }
        $zip = self::readIdentifier($data, 'zip');
        if ($zip !== null) {
            $address->setZip($zip);
        }
        $email = self::readString($data, 'email');
        if ($email !== null) {
            $address->setEmail($email);
        }
        $tel = self::readIdentifier($data, 'tel');
        if ($tel !== null) {
            $address->setTel($tel);
        }
        $fax = self::readIdentifier($data, 'fax');
        if ($fax !== null) {
            $address->setFax($fax);
        }
        $city = self::readString($data, 'city');
        if ($city !== null) {
            $address->setCity($city);
        }
        $country = self::readString($data, 'country');
        if ($country !== null) {
            $address->setCountry($country);
        }
        $state = self::readString($data, 'state');
        if ($state !== null) {
            $address->setState($state);
        }
        return $address;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): Address
    {
        $this->street = $street;
        return $this;
    }

    public function getStreet2(): ?string
    {
        return $this->street2;
    }

    public function setStreet2(?string $street2): Address
    {
        $this->street2 = $street2;
        return $this;
    }

    public function getZip(): string|int|null
    {
        return $this->zip;
    }

    public function setZip(string|int|null $zip): Address
    {
        $this->zip = $zip;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Address
    {
        $this->email = $email;
        return $this;
    }

    public function getTel(): string|int|null
    {
        return $this->tel;
    }

    public function setTel(string|int|null $tel): Address
    {
        $this->tel = $tel;
        return $this;
    }

    public function getFax(): string|int|null
    {
        return $this->fax;
    }

    public function setFax(string|int|null $fax): Address
    {
        $this->fax = $fax;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): Address
    {
        $this->country = $country;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): Address
    {
        $this->state = $state;
        return $this;
    }
}
