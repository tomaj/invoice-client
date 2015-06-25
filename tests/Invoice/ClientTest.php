<?php

namespace Invoice;

use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testCreateClient()
    {
        $item = Client::fromArray([
            'name' => 'test name',
            'company' => 'company s.r.o',
            'vat_number' => 'SK214098724',
            'tax_number' => '214098724',
            'address' => [
                'street' => 'Local Street 245',
                'street2' => '124/2142',
                'zip' => '23021',
                'city' => 'New York',
                'country' => 'USA',
                'tel' => '+420 123 123 421',
                'fax' => '325324324',
                'email' => 'sdsada@asdsads.com',
            ]
        ]);
        $this->assertEquals('test name', $item->getName());
        $this->assertEquals('company s.r.o', $item->getCompany());
        $this->assertEquals('SK214098724', $item->getVatNumber());
        $this->assertEquals('214098724', $item->getTaxNumber());

        $address = $item->getAddress();
        $this->assertEquals('Local Street 245', $address->getStreet());
        $this->assertEquals('124/2142', $address->getStreet2());
        $this->assertEquals('23021', $address->getZip());
        $this->assertEquals('New York', $address->getCity());
        $this->assertEquals('USA', $address->getCountry());
        $this->assertEquals('+420 123 123 421', $address->getTel());
        $this->assertEquals('325324324', $address->getFax());
        $this->assertEquals('sdsada@asdsads.com', $address->getEmail());
    }
}
