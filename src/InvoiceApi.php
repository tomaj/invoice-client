<?php

namespace Invoice;

use Invoice\Response\CreateInvoiceResponse;
use Invoice\Transport\GuzzleTransport;
use Invoice\Transport\TransportInterface;

class InvoiceApi
{
	private $apiToken;

	private $target;

	const DEFAULT_TARGET = 'https://chcemfakturu.sk';

	/**
     *
     * @param string $apiToken   your API token
     * @param array  $options An array of options (since PHP does not support named arguments):
     *                        'target' => 'https://chcemfakturu.sk/' // which API server to use
     *                        'transport' => new \Invoice\GuzzleTransport() // default transport
     *                        'transport' => new \Invoice\NullTransport() // transport that does not send anything
     *                        'debug' => false // default, suppresses throwing of exceptions
     *                        'debug' => true // raises Exceptions on errors
     *                        'logger' => PSR-3 logger (a Psr\Log\LoggerInterface instance)
     * @throws Exception if an error is encountered and debug option is true
     */
	public function __construct($apiToken, $options = [])
	{
		$this->apiToken = $apiToken;

		$debug = false;
        if (array_key_exists('debug', $options)) {
            if ($options['debug']) {
                $debug = true;
            }
        }

        $logger = null;
        if (array_key_exists('logger', $options)) {
            $loggerClass = 'Psr\\Log\\LoggerInterface';
            if (!($options['logger'] instanceof $loggerClass)) {
                if ($debug) {
                    throw new Exception('logger must be an instance of Psr\\Log\\LoggerInterface');
                }
                return;
            }
            $logger = $options['logger'];
        }
        $this->logger = $logger;

        $transport = null;
        if (array_key_exists('transport', $options)) {
            $transport = $options['transport'];
            if ($transport !== null && !($transport instanceof TransportInterface)) {
                throw new Exception('transport must be an instance of Invoice\\TransportInterface');
            }
        } else {
            $transport = new GuzzleTransport();
        }
        $this->transport = $transport;

        $target = null;
        if (array_key_exists('target', $options)) {
            $target = $options['target'];
        }

        if ($target !== null) {
            if (!is_string($target)) {
            	throw new Exception('Target must be string or not specified');
            }
            if (substr($target, 0, 7) !== 'http://' && substr($target, 0, 8) !== 'https://') {
                throw new Exception('Target must be start with http:// or https://');
            }
            $this->target = rtrim($target, '/');
        }
	}

    /**
     * @param Invoice $invoice
     * @return CreateInvoiceResponse
     */
	public function createInvoice(Invoice $invoice)
	{
        $serializer = new Serializer();
        $payload = $serializer->encodeInvoice($invoice);

        $response = $this->transport->call($this->target . '/api/v1/invoices/create', $this->apiToken, $payload, 'post');

        $createInvoiceResponse = new CreateInvoiceResponse($response);

        return $createInvoiceResponse;
	}

//	public function updateInvoice($id, Invoice $invoice)
//	{
//        // todo
//	}

	public function deleteInvoice($id)
	{
        $result = $this->transport->call($this->target . '/api/v1/invoices/delete/' . $id, $this->apiToken, null, 'delete');

        return $result;
	}

	public function getInvoice($id)
	{
        $result = $this->transport->call($this->target . '/api/v1/invoices/detail/' . $id, $this->apiToken, null, 'get');

        // todo spracuj

        return $result;
	}

//	public function loadInvoices()
//	{
//        // todo
//	}
}
