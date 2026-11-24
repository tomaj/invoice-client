---
description: Dajte GuzzleTransport vlastného Guzzle klienta s retry middleware, timeoutom a ďalšou hlavičkou a spustite ho proti padajúcemu a pomalému simulovanému API.
---

# Príklad: opakovanie a timeouty

<ProjectSource name="retries-and-timeouts" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

`GuzzleTransport` prijme akéhokoľvek Guzzle klienta, takže máte k dispozícii všetko, čo Guzzle dokáže: tu retry middleware, 2-sekundový timeout a hlavičku `User-Agent`. Simulované API predstiera server, ktorý dvakrát zlyhá s 503 (`/flaky`), a server, ktorý potrebuje 3 sekundy (`/slow`).

## Kód {#the-code}

<<< @/../examples/retries-and-timeouts/run.php

## Výstup {#output}

<<< @/../examples/retries-and-timeouts/expected-output.txt{text}

## Na čo si všimnúť {#what-to-notice}

- **Opakovanie patrí do Guzzle.** `Middleware::retry()` rozhoduje pre každú požiadavku zvlášť. `GuzzleTransport` nastaví `http_errors` na `false`, takže 503 sa k rozhodovacej funkcii dostane ako odpoveď a po opakovaniach je konečná odpoveď bežnou odpoveďou invoice-client.
- **`POST` nikdy neopakujte naslepo.** Požiadavka na vytvorenie, ktorá skončila timeoutom, už mohla faktúru vytvoriť; jej opakovanie môže vytvoriť duplikát. Rozhodovacia funkcia opakuje len `GET` a `DELETE`.
- **Timeout je `TransportException`.** HTTP odpoveď neexistuje, takže volanie vyhodí výnimku; výnimka z Guzzle je v `getPrevious()` (`NetworkTimeoutException` v Guzzle 8, `ConnectException` v Guzzle 7).
- **Predvolené hlavičky** vášho klienta sa zlúčia s hlavičkou `Authorization`, ktorú pridá transport.

Voľby, ktoré sa oplatí nastaviť, nájdete v [Transportoch](../guide/transports.md#your-own-guzzle-client).
