---
description: Transporty invoice-client - GuzzleTransport s vlastným Guzzle klientom, NullTransport, TestTransport - a ako napísať vlastný, napr. nad PSR-18.
---

# Transporty

Transport pošle jednu HTTP požiadavku a jej stavový kód a telo vráti ako `Transport\Result`. `InvoiceApi` používa `GuzzleTransport`, pokiaľ mu ako `transport` neodovzdáte iný.

| Transport | Posiela | Použitie |
| --- | --- | --- |
| `GuzzleTransport` | skutočné HTTP požiadavky cez Guzzle 7 alebo 8 | produkcia, predvolený |
| `TestTransport` | nič; vracia odpovede, ktoré zaradíte do fronty, a zaznamenáva požiadavky | vaše testy, pozrite [Testovanie](./testing.md) |
| `NullTransport` | nič; každé volanie vráti odpoveď so stavom `0` a `No request was sent` | vypnutie fakturácie, napr. v stagingu |

## GuzzleTransport

```php
use Tomaj\InvoiceClient\Transport\GuzzleTransport;

new GuzzleTransport();                 // nový Guzzle klient s 10-sekundovým timeoutom
new GuzzleTransport($yourGuzzleClient); // ľubovoľný GuzzleHttp\ClientInterface
```

Pri každej požiadavke:

- nastaví `Authorization: Bearer <token>` (zlúčené s predvolenými hlavičkami vášho klienta),
- ak je telo (`createInvoice()`), pošle JSON ako telo s `Content-Type: application/json`; JSON sa pošle tak, ako je, znova sa nekóduje. `GET` a `DELETE` nemajú telo ani `Content-Type`,
- nastaví voľbu požiadavky `http_errors` na `false`, takže odpoveď 4xx alebo 5xx sa vráti ako `Result`, nie ako výnimka z Guzzle, bez ohľadu na nastavenie vášho klienta,
- každú `GuzzleHttp\Exception\GuzzleException` (žiadna odpoveď) premení na `TransportException` s výnimkou z Guzzle v `getPrevious()`.

`GuzzleTransport::TIMEOUT` (`10`) je `timeout` predvoleného klienta v sekundách. Váš vlastný klient si ponechá timeouty, ktoré mu nastavíte.

### Vlastný Guzzle klient {#your-own-guzzle-client}

Nakonfigurovaného klienta odovzdajte pre iné timeouty, proxy, ďalšie hlavičky alebo middleware:

<<< @/snippets/transport-options.php

Užitočné [voľby požiadavky Guzzle](https://docs.guzzlephp.org/en/stable/request-options.html):

| Voľba | Prečo |
| --- | --- |
| `timeout`, `connect_timeout` | ako dlho čakať na API; timeout sa zmení na `TransportException` |
| `headers` | napr. `User-Agent` s názvom vašej aplikácie (`Authorization` a pri tele aj `Content-Type` nastaví transport vždy sám) |
| `proxy` | odchádzajúca proxy |
| `handler` | `HandlerStack` s middleware: opakovanie, história na ladenie, `MockHandler` v testoch |

[Príklad retries-and-timeouts](../examples/retries-and-timeouts.md) zostaví klienta s retry middleware, timeoutom a ďalšou hlavičkou a spustí ho proti padajúcemu a pomalému simulovanému API.

::: danger Overovanie TLS nechajte zapnuté
Na klientovi, ktorý nesie váš API token, nenastavujte `verify` na `false`: ktokoľvek na sieťovej ceste by si ho mohol prečítať.
:::

## NullTransport

```php
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\NullTransport;

$api = new InvoiceApi('unused', transport: new NullTransport());
$api->createInvoice($invoice)->getErrorMessage(); // 'No request was sent'
```

`call()` vráti `null`; každá odpoveď potom hlási stav `0` a `No request was sent` a `isOk()` je `false`, takže váš kód prejde chybovou vetvou bez sieťového volania.

## TestTransport

Zaznamenáva každú požiadavku a vracia odpovede, ktoré zaradíte do fronty, v poradí. S príkladmi ho popisuje [Testovanie integrácie](./testing.md#testtransport).

## Vlastný transport {#writing-a-transport}

Implementujte `Transport\TransportInterface`:

<!-- snippet: skip -->
```php
public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result;
```

| Argument | Význam |
| --- | --- |
| `$method` | `HttpMethod::Get`, `Post` alebo `Delete`; `->value` je reťazec HTTP metódy |
| `$url` | celá URL, id je už zakódované |
| `$apiToken` | pošlite ho ako `Authorization: Bearer <token>`; nikdy ho nelogujte |
| `$payload` | telo JSON alebo `null`, ak telo nie je; pošlite ho s `Content-Type: application/json` |

Každú HTTP odpoveď vráťte ako `new Result($statusCode, $body)`, vrátane 4xx a 5xx. `TransportException` vyhoďte len vtedy, keď žiadna odpoveď neprišla. `null` vráťte len vtedy, ak váš transport zámerne nič neposiela.

Transport nad ľubovoľným PSR-18 HTTP klientom:

```php
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Transport\HttpMethod;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TransportInterface;

final readonly class Psr18Transport implements TransportInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requests,
        private StreamFactoryInterface $streams,
    ) {}

    public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result
    {
        $request = $this->requests->createRequest($method->value, $url)
            ->withHeader('Authorization', 'Bearer ' . $apiToken);
        if ($payload !== null && $payload !== '') {
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->streams->createStream($payload));
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new TransportException(sprintf('%s %s failed: %s', $method->value, $url, $e->getMessage()), 0, $e);
        }

        return new Result($response->getStatusCode(), (string) $response->getBody());
    }
}
```
