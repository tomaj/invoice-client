<?php

declare(strict_types=1);

namespace Invoice;

use Invoice\Response\CreateInvoiceResponse;
use Invoice\Transport\GuzzleTransport;
use Invoice\Transport\Result;
use Invoice\Transport\TransportInterface;

class InvoiceApi
{
    public const DEFAULT_TARGET = 'https://chcemfakturu.sk';

    private string $apiToken;

    private string $target;

    /** @var object|null a Psr\Log\LoggerInterface instance */
    private ?object $logger = null;

    private ?TransportInterface $transport = null;

    /**
     * @param string $apiToken your API token
     * @param array<string, mixed> $options An array of options:
     *                        'target' => 'https://chcemfakturu.sk/' // which API server to use
     *                        'transport' => new \Invoice\GuzzleTransport() // default transport
     *                        'transport' => new \Invoice\NullTransport() // transport that does not send anything
     *                        'debug' => false // default, suppresses throwing of exceptions
     *                        'debug' => true // raises Exceptions on errors
     *                        'logger' => PSR-3 logger (a Psr\Log\LoggerInterface instance)
     * @throws Exception if an error is encountered and debug option is true
     */
    public function __construct(string $apiToken, array $options = [])
    {
        $this->apiToken = $apiToken;
        $this->target = self::DEFAULT_TARGET;

        $debug = (bool) ($options['debug'] ?? false);

        if (array_key_exists('logger', $options)) {
            $loggerClass = 'Psr\\Log\\LoggerInterface';
            if (!($options['logger'] instanceof $loggerClass)) {
                if ($debug) {
                    throw new Exception('logger must be an instance of Psr\\Log\\LoggerInterface');
                }
                return;
            }
            $this->logger = $options['logger'];
        }

        if (array_key_exists('transport', $options)) {
            $transport = $options['transport'];
            if ($transport !== null && !($transport instanceof TransportInterface)) {
                throw new Exception('transport must be an instance of Invoice\\TransportInterface');
            }
        } else {
            $transport = new GuzzleTransport();
        }
        $this->transport = $transport;

        $target = $options['target'] ?? null;

        if ($target !== null) {
            if (!is_string($target)) {
                throw new Exception('Target must be string or not specified');
            }
            if (!str_starts_with($target, 'http://') && !str_starts_with($target, 'https://')) {
                throw new Exception('Target must be start with http:// or https://');
            }
            $this->target = rtrim($target, '/');
        }
    }

    public function getLogger(): ?object
    {
        return $this->logger;
    }

    public function createInvoice(Invoice $invoice): CreateInvoiceResponse
    {
        $serializer = new Serializer();
        $payload = $serializer->encodeInvoice($invoice);

        $response = $this->transport()->call($this->target . '/api/v1/invoices/create', $this->apiToken, $payload, 'post');

        return new CreateInvoiceResponse($response ?? new Result(0, ''));
    }

    public function htmlUrl(string $id): string
    {
        return $this->target . '/html/' . $id;
    }

    public function pdfUrl(string $id): string
    {
        return $this->target . '/pdf/' . $id;
    }

    public function pdfDownloadUrl(string $id): string
    {
        return $this->target . '/pdf-download/' . $id;
    }

    public function deleteInvoice(string $id): ?Result
    {
        return $this->transport()->call($this->target . '/api/v1/invoices/delete/' . $id, $this->apiToken, null, 'delete');
    }

    public function getInvoice(string $id): ?Result
    {
        // todo spracuj
        return $this->transport()->call($this->target . '/api/v1/invoices/detail/' . $id, $this->apiToken, null, 'get');
    }

    private function transport(): TransportInterface
    {
        if ($this->transport === null) {
            throw new Exception('No transport configured');
        }

        return $this->transport;
    }
}
