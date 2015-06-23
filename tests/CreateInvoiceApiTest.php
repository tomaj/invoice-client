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
        $invoice
            ->setNumber('200011')
            ->setName('Invoice 200011')
            ->setPrice(123.3)
            ->setDescription('invoice description')
            ->setVariableSymbol('123213');

        $dummyResponse = '{"status": "ok", "download_pdf_url": "http://download.com/", "invoice_id": "231r08iyfouihg3rt3r", "html_url": "http://html.com/"}';
        $result = new Result(200, $dummyResponse);
        $transport->setResponse($result);

        $result = $invoiceApi->createInvoice($invoice);

        // check what client sent
        $payload = $transport->getPayload();
        $data = json_decode($payload, true);

        $this->assertEquals(123.3, $data['price']);
        $this->assertEquals('123213', $data['variable_symbol']);
        $this->assertEquals('200011', $data['number']);
        $this->assertEquals('Invoice 200011', $data['name']);
        $this->assertEquals('invoice description', $data['description']);

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
