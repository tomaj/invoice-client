<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Tomaj\InvoiceClient\Exception\InvalidValueException;
use Tomaj\InvoiceClient\Exception\SerializationException;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Response\CreateInvoiceResponse;
use Tomaj\InvoiceClient\Response\DeleteInvoiceResponse;
use Tomaj\InvoiceClient\Response\InvoiceDetailResponse;
use Tomaj\InvoiceClient\Response\ResponseInterface;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;
use Tomaj\InvoiceClient\Transport\HttpMethod;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TransportInterface;

/**
 * Client of the chcemfakturu.sk invoicing API.
 *
 * Every API call returns a response object: check isOk(), an API error (4xx, 5xx, "status": "error") is not an exception.
 * Exceptions are thrown only when no HTTP response arrives (TransportException) or the invoice cannot be encoded.
 */
final readonly class InvoiceApi
{
    public const string DEFAULT_TARGET = 'https://chcemfakturu.sk';

    private string $target;

    private TransportInterface $transport;

    private LoggerInterface $logger;

    private Serializer $serializer;

    /**
     * @param string $apiToken your API token, sent as a bearer token and never logged
     * @param string $target base URL of the API server, http:// or https://
     * @param TransportInterface|null $transport defaults to GuzzleTransport
     * @param LoggerInterface|null $logger PSR-3 logger for requests, status codes and API errors (never the token or invoice data)
     * @throws InvalidValueException when the target does not start with http:// or https://
     */
    public function __construct(
        #[\SensitiveParameter]
        private string $apiToken,
        string $target = self::DEFAULT_TARGET,
        ?TransportInterface $transport = null,
        ?LoggerInterface $logger = null,
    ) {
        if (!str_starts_with($target, 'http://') && !str_starts_with($target, 'https://')) {
            throw new InvalidValueException(sprintf("Target must start with http:// or https://, '%s' given", $target));
        }
        $this->target = rtrim($target, '/');
        $this->transport = $transport ?? new GuzzleTransport();
        $this->logger = $logger ?? new NullLogger();
        $this->serializer = new Serializer();
    }

    /**
     * @throws SerializationException when the invoice cannot be encoded as JSON
     * @throws TransportException when no HTTP response arrives
     */
    public function createInvoice(Invoice $invoice): CreateInvoiceResponse
    {
        $payload = $this->serializer->encodeInvoice($invoice);
        $result = $this->send(HttpMethod::Post, '/api/v1/invoices/create', $payload);

        return $this->logged(new CreateInvoiceResponse($result), HttpMethod::Post, '/api/v1/invoices/create');
    }

    /**
     * @throws TransportException when no HTTP response arrives
     */
    public function getInvoice(string $id): InvoiceDetailResponse
    {
        $path = '/api/v1/invoices/detail/' . rawurlencode($id);

        return $this->logged(new InvoiceDetailResponse($this->send(HttpMethod::Get, $path)), HttpMethod::Get, $path);
    }

    /**
     * @throws TransportException when no HTTP response arrives
     */
    public function deleteInvoice(string $id): DeleteInvoiceResponse
    {
        $path = '/api/v1/invoices/delete/' . rawurlencode($id);

        return $this->logged(new DeleteInvoiceResponse($this->send(HttpMethod::Delete, $path)), HttpMethod::Delete, $path);
    }

    public function htmlUrl(string $id): string
    {
        return $this->target . '/html/' . rawurlencode($id);
    }

    public function pdfUrl(string $id): string
    {
        return $this->target . '/pdf/' . rawurlencode($id);
    }

    public function pdfDownloadUrl(string $id): string
    {
        return $this->target . '/pdf-download/' . rawurlencode($id);
    }

    /**
     * @throws TransportException
     */
    private function send(HttpMethod $method, string $path, ?string $payload = null): ?Result
    {
        $context = ['method' => $method->value, 'url' => $this->target . $path];
        $this->logger->debug('Invoice API request', $context);

        try {
            return $this->transport->call($method, $this->target . $path, $this->apiToken, $payload);
        } catch (TransportException $e) {
            $this->logger->error('Invoice API request failed', $context + ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * @template T of ResponseInterface
     * @param T $response
     * @return T
     */
    private function logged(ResponseInterface $response, HttpMethod $method, string $path): ResponseInterface
    {
        $context = ['method' => $method->value, 'url' => $this->target . $path, 'status' => $response->getStatusCode()];
        if ($response->isOk()) {
            $this->logger->info('Invoice API response', $context);
        } else {
            $this->logger->warning('Invoice API error: {error}', $context + ['error' => $response->getErrorMessage()]);
        }

        return $response;
    }
}
