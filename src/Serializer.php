<?php

declare(strict_types=1);

namespace Invoice;

class Serializer
{
    /**
     * @throws \JsonException
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
            'currency' => $invoice->getCurrency(),
        ];
        $client = $invoice->getClient();
        if ($client !== null) {
            $data['client'] = $this->encodeClient($client);
        }
        $shippingAddress = $invoice->getShippingAddress();
        if ($shippingAddress !== null) {
            $data['shipping_address'] = $this->encodeAddress($shippingAddress);
        }
        $discount = $invoice->getDiscount();
        if ($discount !== null && $discount->getType() !== 'none') {
            $data['discount'] = [
                'type' => $discount->getType(),
                'value' => $discount->getValue(),
            ];
        }
        return json_encode(['invoice' => $data], JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    public function encodeClient(Client $client): array
    {
        $data = [
            'name' => $client->getName(),
            'company' => $client->getCompany(),
            'identifier' => $client->getIdentifier(),
            'vat_number' => $client->getVatNumber(),
            'tax_number' => $client->getTaxNumber(),
        ];
        $address = $client->getAddress();
        if ($address !== null) {
            $data['address'] = $this->encodeAddress($address);
        }
        return $data;
    }

    /**
     * @return array<string, string|int|null>
     */
    public function encodeAddress(Address $address): array
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
    public function encodeItems(array $items): array
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
            $discount = $item->getDiscount();
            if ($discount !== null && $discount->getType() !== 'none') {
                $row['discount'] = [
                    'type' => $discount->getType(),
                    'value' => $discount->getValue(),
                ];
            }
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @throws \JsonException
     */
    public function decodeInvoice(string $paylod): mixed
    {
        return json_decode($paylod, true, 512, JSON_THROW_ON_ERROR);
    }
}
