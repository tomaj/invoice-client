<?php

namespace Invoice;

class Address
{
    private $street;

    private $street2;

    private $zip;

    private $email;

    private $tel;

    private $fax;

    private $city;

    private $country;

    private $state;

    public static function fromArray($data)
    {
        $address = new Address();
        if (isset($data['street'])) {
            $address->setStreet($data['street']);
        }
        if (isset($data['street2'])) {
            $address->setStreet2($data['street2']);
        }
        if (isset($data['zip'])) {
            $address->setZip($data['zip']);
        }
        if (isset($data['city'])) {
            $address->setCity($data['city']);
        }
        if (isset($data['country'])) {
            $address->setCountry($data['country']);
        }
        if (isset($data['state'])) {
            $address->setState($data['state']);
        }
        if (isset($data['tel'])) {
            $address->setTel($data['tel']);
        }
        if (isset($data['fax'])) {
            $address->setFax($data['fax']);
        }
        if (isset($data['email'])) {
            $address->setEmail($data['email']);
        }
        return $address;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @param string $street2
     * @return Address
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;
        return $this;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     * @return Address
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Address
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param string $tel
     * @return Address
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
        return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     * @return Address
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Address
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }
}
