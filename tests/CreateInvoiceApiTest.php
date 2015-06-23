<?php

namespace Invoice;

use Invoice\Transport\Result;
use Invoice\Transport\TestTransport;
use PHPUnit_Framework_TestCase;

class InvoiceApiTest extends PHPUnit_Framework_TestCase
{
    public function testCreateInvoiceItem()
    {
        $transport = new TestTransport();
        $invoiceApi = new InvoiceApi('941240975320975', [
            'transport' => $transport,
        ]);
        $invoice = new Invoice();
        $invoiceItem = new InvoiceItem();
        $invoiceItem
            ->setQuantity(2)
            ->setPrice(2.3)
            ->setVat(20)
            ->setDiscount(new Discount('percent', 20));
        $invoice
            ->setNumber('200011')
            ->setName('Invoice 200011')
            ->setDescription('invoice description')
            ->setVariableSymbol('123213')
            ->addItem($invoiceItem);

        $dummyResponse = '{"status": "ok", "download_pdf_url": "http://download.com/", "invoice_id": "231r08iyfouihg3rt3r", "html_url": "http://html.com/"}';
        $result = new Result(200, $dummyResponse);
        $transport->setResponse($result);

        $result = $invoiceApi->createInvoice($invoice);

        // check what client sent
        $payload = $transport->getPayload();
        $data = json_decode($payload, true);
        $data = $data['invoice'];

        $this->assertEquals('123213', $data['variable_symbol']);
        $this->assertEquals('200011', $data['number']);
        $this->assertEquals('Invoice 200011', $data['name']);
        $this->assertEquals('invoice description', $data['description']);
        $this->assertEquals(2, $data['items'][0]['quantity']);
        $this->assertEquals(20, $data['items'][0]['vat']);
        $this->assertEquals(2.3, $data['items'][0]['price']);
        $this->assertEquals('percent', $data['items'][0]['discount']['type']);
        $this->assertEquals(20, $data['items'][0]['discount']['value']);

        // check what client read
        $this->assertEquals('231r08iyfouihg3rt3r', $result->getId());
        $this->assertEquals('http://download.com/', $result->getDownloadUrl());
        $this->assertEquals('http://html.com/', $result->getHtmlUrl());
    }

    public function testCreateInvoiceErrorResponse()
    {

    }

//    public function testGetInvoice()
//    {
//        $transport = new TestTransport();
//        $invoiceApi = new InvoiceApi('941240975320975', [
//            'transport' => $transport,
//        ]);
//
//        $result = $invoiceApi->getInvoice('sadoywaf08yesfo8ysgreg');
//
//    }
//
//    public function testDeleteInvoice()
//    {
//        $transport = new TestTransport();
//        $invoiceApi = new InvoiceApi('941240975320975', [
//            'transport' => $transport,
//        ]);
//
//        $result = $invoiceApi->deleteInvoice('sadoywaf08yesfo8ysgreg');
//    }
}
