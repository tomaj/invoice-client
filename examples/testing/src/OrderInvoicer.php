<?php

declare(strict_types=1);

namespace App;

use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;

/**
 * Your application code: turns a paid order into an invoice and returns the link for the customer.
 */
final readonly class OrderInvoicer
{
    public function __construct(private InvoiceApi $api) {}

    /**
     * @param array{number: string, lines: list<array{product: string, quantity: int, price: string}>} $order
     * @throws InvoicingFailed when the API does not create the invoice
     */
    public function invoice(array $order): string
    {
        $invoice = new Invoice()
            ->setNumber($order['number'])
            ->setName('Invoice ' . $order['number'])
            ->setVariableSymbol($order['number'])
            ->setCurrency(Currency::EUR);
        foreach ($order['lines'] as $line) {
            $invoice->addItem(
                new InvoiceItem()
                    ->setDescription($line['product'])
                    ->setQuantity($line['quantity'])
                    ->setPriceItem($line['price'])
                    ->setVat(20),
            );
        }

        $response = $this->api->createInvoice($invoice);
        if (!$response->isOk()) {
            throw new InvoicingFailed(sprintf('Order %s: %s (HTTP %d)', $order['number'], $response->getErrorMessage(), $response->getStatusCode()));
        }

        return (string) $response->getDownloadUrl();
    }
}
