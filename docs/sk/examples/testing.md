---
description: PHPUnit testy aplikačnej služby, ktorá používa invoice-client, s TestTransport a Guzzle MockHandler, bez siete.
---

# Príklad: testovanie s PHPUnit

<ProjectSource name="testing" :files="['src/OrderInvoicer.php', 'tests/OrderInvoicerTest.php', 'phpunit.xml', 'expected-output.txt', 'e2e.sh']" />

Testy potrebuje váš kód, nie klient API. `OrderInvoicer` je typická aplikačná služba: z uhradenej objednávky vytvorí faktúru a vráti odkaz na PDF, alebo vyhodí vlastnú výnimku, keď API faktúru odmietne. Testy overia, čo posiela a ako spracuje každú odpoveď, bez akejkoľvek siete.

## Služba {#the-service}

<<< @/../examples/testing/src/OrderInvoicer.php

## Testy {#the-tests}

<<< @/../examples/testing/tests/OrderInvoicerTest.php

## Výstup {#output}

Zoznam testov tak, ako ho vypíše `phpunit --testdox` (`e2e.sh` vynechá riadky s verziou, časom a pamäťou):

<<< @/../examples/testing/expected-output.txt{text}

## Na čo si všimnúť {#what-to-notice}

- **`TestTransport`** vracia odpovede z fronty v poradí a zaznamenáva každú požiadavku: metódu, URL, token a JSON dáta. Dekódujte `payload`, ak chcete overiť, čo váš kód poslal.
- **Žiadna odpoveď vo fronte** znamená žiadnu požiadavku: odpoveď hlási stav `0` a `No request was sent`. Tak otestujete, ako váš kód reaguje na nedostupnú fakturáciu.
- **`MockHandler`** ide o úroveň nižšie a spúšťa skutočný `GuzzleTransport`, takže môžete overiť HTTP hlavičky, napríklad `Authorization`.

Viac v [Testovaní integrácie](../guide/testing.md).
