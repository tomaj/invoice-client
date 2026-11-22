<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient;

use Tomaj\InvoiceClient\Exception\SerializationException;
use Tomaj\InvoiceClient\Invoice\Address;
use Tomaj\InvoiceClient\Invoice\Client;
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;

/**
 * Encodes an invoice to the JSON request body of the API.
 */
final class Serializer
{
    /**
     * @throws SerializationException when the invoice cannot be encoded, for example because a text is not valid UTF-8
     */
    public function encodeInvoice(Invoice $invoice): string
    {
        $data = [
            'id' => $invoice->getId(),
            'number' => $invoice->getNumber(),
            'name' => $invoice->getName(),
            'created' => $invoice->getCreated(),
            'delivered' => $invoice->getDelivered(),
            'due' => $invoice->getDue(),
            'status' => $invoice->getStatus(),
            'variable_symbol' => $invoice->getVariableSymbol(),
            'constant_symbol' => $invoice->getConstantSymbol(),
            'description' => $invoice->getDescription(),
            'items' => $this->encodeItems($invoice->getItems()),
            'price' => $invoice->getPrice(),
            'price_total' => $invoice->getPriceTotal(),
            'currency' => $invoice->getCurrency()?->value,
        ];
        $client = $invoice->getClient();
        if ($client instanceof Client) {
            $data['client'] = $this->encodeClient($client);
        }
        $shippingAddress = $invoice->getShippingAddress();
        if ($shippingAddress instanceof Address) {
            $data['shipping_address'] = $this->encodeAddress($shippingAddress);
        }
        $discount = $this->encodeDiscount($invoice->getDiscount());
        if ($discount !== null) {
            $data['discount'] = $discount;
        }

        try {
            return json_encode(['invoice' => $data], JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new SerializationException('Cannot encode the invoice as JSON: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function encodeClient(Client $client): array
    {
        $data = [
            'name' => $client->getName(),
            'company' => $client->getCompany(),
            'identifier' => $client->getIdentifier(),
            'vat_number' => $client->getVatNumber(),
            'tax_number' => $client->getTaxNumber(),
        ];
        $address = $client->getAddress();
        if ($address instanceof Address) {
            $data['address'] = $this->encodeAddress($address);
        }
        return $data;
    }

    /**
     * @return array<string, string|int|null>
     */
    private function encodeAddress(Address $address): array
    {
        return [
            'street' => $address->getStreet(),
            'street2' => $address->getStreet2(),
            'zip' => $address->getZip(),
            'city' => $address->getCity(),
            'country' => $address->getCountry(),
            'state' => $address->getState(),
            'email' => $address->getEmail(),
            'tel' => $address->getTel(),
            'fax' => $address->getFax(),
        ];
    }

    /**
     * @param array<InvoiceItem> $items
     * @return list<array<string, mixed>>
     */
    private function encodeItems(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            $row = [
                'vat' => $item->getVat(),
                'quantity' => $item->getQuantity(),
                'description' => $item->getDescription(),
                'price_item' => $item->getPriceItem(),
                'price' => $item->getPrice(),
                'price_total' => $item->getPriceTotal(),
            ];
            $discount = $this->encodeDiscount($item->getDiscount());
            if ($discount !== null) {
                $row['discount'] = $discount;
            }
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @return array{type: string, value: int|float|numeric-string}|null
     */
    private function encodeDiscount(?Discount $discount): ?array
    {
        if (!$discount instanceof Discount || $discount->getType() === DiscountType::None) {
            return null;
        }

        return [
            'type' => $discount->getType()->value,
            'value' => $discount->getValue(),
        ];
    }
}
