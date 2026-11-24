---
description: Prehľad konfigurácie invoice-client - každý argument InvoiceApi a GuzzleTransport s typom, predvolenou hodnotou a významom.
---

# Prehľad konfigurácie

invoice-client nemá konfiguračný súbor a nečíta žiadne premenné prostredia. Všetko je argumentom `InvoiceApi` alebo transportu.

## InvoiceApi

<!-- snippet: skip -->
```php
new InvoiceApi(
    string $apiToken,
    string $target = InvoiceApi::DEFAULT_TARGET,
    ?TransportInterface $transport = null,
    ?LoggerInterface $logger = null,
);
```

| Argument | Typ | Predvolená hodnota | Význam |
| --- | --- | --- | --- |
| `apiToken` | `string` | povinný | API token vášho účtu na chcemfakturu.sk, posiela sa ako `Authorization: Bearer <token>`. Označený `#[\SensitiveParameter]`, nikdy sa neloguje |
| `target` | `string` | `InvoiceApi::DEFAULT_TARGET` = `https://chcemfakturu.sk` | základná URL API. Musí začínať `http://` alebo `https://`, inak `InvalidValueException`. Koncová `/` sa odstráni. `http://` používajte len pre lokálny testovací server |
| `transport` | `?Transport\TransportInterface` | `null`: nový `GuzzleTransport` | ako sa požiadavky posielajú, pozrite [Transporty](./transports.md) |
| `logger` | `?Psr\Log\LoggerInterface` | `null`: `NullLogger` | dostáva požiadavky, stavové kódy a chyby API, pozrite [Logovanie](./logging.md) |

`InvoiceApi` je `final readonly`: vytvorte ho raz (napríklad ako službu vo vašom kontajneri) a používajte opakovane. Medzi volaniami si nedrží žiadny stav.

Pomenované argumenty robia zámer zrozumiteľným:

```php
$api = new InvoiceApi(
    apiToken: (string) getenv('CHCEMFAKTURU_API_TOKEN'),
    target: 'https://chcemfakturu.sk',
    transport: new GuzzleTransport($guzzle),
    logger: $logger,
);
```

## GuzzleTransport

<!-- snippet: skip -->
```php
new GuzzleTransport(?GuzzleHttp\ClientInterface $client = null);
```

| Argument | Typ | Predvolená hodnota | Význam |
| --- | --- | --- | --- |
| `client` | `?GuzzleHttp\ClientInterface` | `null`: `new GuzzleHttp\Client(['timeout' => GuzzleTransport::TIMEOUT])` | váš nakonfigurovaný Guzzle 7 alebo 8 klient: timeouty, proxy, hlavičky, middleware |

| Konštanta | Hodnota | Význam |
| --- | --- | --- |
| `GuzzleTransport::TIMEOUT` | `10` | celkový timeout predvoleného klienta v sekundách |

Nech odovzdáte akéhokoľvek klienta, každá požiadavka dostane hlavičku `Authorization` a `http_errors => false`. Podrobnosti v časti [Transporty](./transports.md#guzzletransport).

## Ďalšie transporty {#other-transports}

| Trieda | Konštruktor | Poznámky |
| --- | --- | --- |
| `TestTransport` | bez argumentov | odpovede zaradíte do fronty cez `addResponse()`; pozrite [Testovanie](./testing.md) |
| `NullTransport` | bez argumentov | nič neposiela |

## Ukážkové nastavenia {#example-setups}

::: code-group

```php [Produkcia]
$api = new InvoiceApi(
    (string) getenv('CHCEMFAKTURU_API_TOKEN'),
    logger: $logger,
);
```

```php [Kratší timeout]
$api = new InvoiceApi(
    (string) getenv('CHCEMFAKTURU_API_TOKEN'),
    transport: new GuzzleTransport(new GuzzleHttp\Client(['connect_timeout' => 2, 'timeout' => 5])),
);
```

```php [Lokálne simulované API]
$api = new InvoiceApi('test-token', target: 'http://127.0.0.1:8701');
```

```php [Vypnutá fakturácia]
$api = new InvoiceApi('unused', transport: new NullTransport());
```

:::
