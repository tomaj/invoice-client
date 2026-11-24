---
description: Bezpečnosť invoice-client - API token, HTTPS a overovanie TLS, osobné údaje vo faktúrach a ako nahlásiť zraniteľnosť.
---

# Bezpečnosť

## Nahlásenie zraniteľnosti {#reporting-a-vulnerability}

Pre bezpečnostný problém nezakladajte verejné issue. Nahláste ho súkromne cez [GitHub private vulnerability reporting](https://github.com/tomaj/invoice-client/security/advisories/new) alebo e-mailom podľa [bezpečnostnej politiky](https://github.com/tomaj/invoice-client/blob/master/SECURITY.md). Do hlásenia nikdy nedávajte skutočný API token ani skutočné údaje odberateľov.

## Podporované verzie {#supported-versions}

| Verzia | Podporovaná |
| --- | --- |
| 1.x | áno, po vydaní 1.0.0: opravy chýb a bezpečnostné opravy |
| `dev-master` pred 1.0.0 | nie: prejdite na 1.0.0 ([návod na prechod](../upgrade.md)) |

## API token {#the-api-token}

Token je bearer tajomstvo: kto ho má, môže vytvárať, čítať a mazať faktúry vášho účtu.

- Držte ho v prostredí alebo v úložisku tajomstiev, nikdy nie v repozitári ani vo front-endovom kóde.
- Klient ho posiela len v hlavičke `Authorization`. Nikdy sa neodovzdá PSR-3 loggeru a argument konštruktora je označený `#[\SensitiveParameter]`, takže je v stack trace skrytý.
- V produkcii nezapínajte voľbu Guzzle `debug` ani nelogujte celé požiadavky: vypísala by sa hlavička.
- Ak token unikol, vygenerujte nový v nastaveniach svojho účtu.

## HTTPS a TLS {#https-and-tls}

- Predvolený cieľ je `https://chcemfakturu.sk`. Klient prijme aj cieľ `http://`, ale len pre lokálny testovací server: cez obyčajné HTTP ide token nešifrovane.
- Overovanie TLS nechajte zapnuté. Ak `GuzzleTransport` odovzdáte vlastného Guzzle klienta, nikdy nenastavujte `verify` na `false`.

## Osobné údaje {#personal-data}

Faktúry obsahujú mená, adresy, e-maily a daňové čísla vašich odberateľov.

- Klient loguje len HTTP metódu, URL (ktorá obsahuje id faktúry), stavový kód a chybovú správu z API, nikdy telo požiadavky ani odpovede.
- `getBody()` odpovede môže obsahovať údaje faktúry; nedávajte ho do zdieľaných logov.
- Vlastný transport alebo logger musí dodržiavať rovnaké pravidlá.

## V testoch {#in-tests}

Použite `TestTransport`, Guzzle `MockHandler` alebo lokálne simulované API z [príkladov](../examples/). Testy nespúšťajte so skutočným tokenom: vytvárali by skutočné faktúry.
