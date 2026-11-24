---
description: Čo PSR-3 logger dostane od InvoiceApi pri úspešnom volaní, chybe API a zlyhaní spojenia, a dôkaz, že sa token nikdy neloguje.
---

# Príklad: logovanie

<ProjectSource name="logging" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

Drobný PSR-3 logger, ktorý vypíše každý záznam, ukazuje, čo `InvoiceApi` loguje. Vo svojej aplikácii namiesto neho odovzdajte Monolog alebo logger vášho frameworku; pozrite [Logovanie](../guide/logging.md).

## Kód {#the-code}

<<< @/../examples/logging/run.php

## Výstup {#output}

<<< @/../examples/logging/expected-output.txt{text}

## Na čo si všimnúť {#what-to-notice}

- Každá požiadavka sa zaloguje na úrovni `debug` pred odoslaním a jej výsledok na `info` (OK) alebo `warning` (chyba API) so stavovým kódom.
- Zlyhanie spojenia sa zaloguje na úrovni `error` s `TransportException` v kontexte a potom sa výnimka vyhodí do vášho kódu.
- Správa varovania má PSR-3 zástupný symbol, `Invoice API error: {error}`; logger vyššie ho doplní, rovnako ako `PsrLogMessageProcessor` z Monologu.
- Posledné dva riadky skontrolujú celý log: nie je v ňom token ani žiadny text faktúry.

Všetky úrovne, správy a kľúče kontextu vymenúva [Logovanie](../guide/logging.md).
