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
            'vat' => 'SK214098724',
            'local_vat' => '214098724',
            'address' => [
                'street' => 'Local Street 245',
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
        $this->assertEquals('SK214098724', $item->getVat());
        $this->assertEquals('214098724', $item->getLocalVat());

        $address = $item->getAddress();
        $this->assertEquals('Local Street 245', $address->getStreet());
        $this->assertEquals('23021', $address->getZip());
        $this->assertEquals('New York', $address->getCity());
        $this->assertEquals('USA', $address->getCountry());
        $this->assertEquals('+420 123 123 421', $address->getTel());
        $this->assertEquals('325324324', $address->getFax());
        $this->assertEquals('sdsada@asdsads.com', $address->getEmail());
    }
}
