<?php

namespace Invoice;

class Serializer
{
    public function encodeInvoice(Invoice $invoice)
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
        ];
        if ($invoice->getClient()) {
            $data['client'] = $this->encodeClient($invoice->getClient());
        }
        if ($invoice->getShippingAddress()) {
            $data['shipping_address'] = $this->encodeAddress($invoice->getShippingAddress());
        }
        if ($invoice->getDiscount() && $invoice->getDiscount()->getType() != 'none') {
            $data['discount'] = [
                'type' => $invoice->getDiscount()->getType(),
                'value' => $invoice->getDiscount()->getValue(),
            ];
        }
        return json_encode(['invoice' => $data]);
    }

    public function encodeClient(Client $client)
    {
        $data = [
            'identifier' => $client->getIdentifier(),
            'name' => $client->getName(),
            'company' => $client->getCompany(),
            'vat' => $client->getVat(),
        ];
        if ($client->getAddress()) {
            $data['address'] = $this->encodeAddress($client->getAddress());
        }
        return $data;
    }

    public function encodeAddress(Address $address)
    {
        return [
            'street' => $address->getStreet(),
            'zip' => $address->getZip(),
            'city' => $address->getCity(),
            'country' => $address->getCountry(),
            'state' => $address->getState(),
            'email' => $address->getEmail(),
            'tel' => $address->getTel(),
            'fax' => $address->getZip(),
        ];
    }

    public function encodeItems(array $items)
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
            if ($item->getDiscount() && $item->getDiscount()->getType() != 'none') {
                $row['discount'] = [
                    'type' => $item->getDiscount()->getType(),
                    'value' => $item->getDiscount()->getValue(),
                ];
            }
            $result[] = $row;
        }
        return $result;
    }

    public function decodeInvoice($paylod)
    {
        return json_decode($paylod, true);
    }
}
