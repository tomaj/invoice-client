---
description: Čo sa deje medzi vaším volaním InvoiceApi a objektom odpovede, ktorý vráti, krok za krokom.
---

# Ako to funguje

<Diagram name="flow" alt="Váš kód zostaví Invoice a zavolá InvoiceApi, ktoré ju serializuje do JSON a odovzdá transportu; transport ju pošle do API chcemfakturu.sk s bearer tokenom a vráti HTTP stav a telo ako Result; InvoiceApi z neho vytvorí objekt odpovede. Chýbajúca HTTP odpoveď vyhodí TransportException; neplatný vstup vyhodí výnimku ešte pred požiadavkou." />

## Časti {#the-parts}

| Časť | Trieda | Úloha |
| --- | --- | --- |
| Dáta | `Invoice\Invoice`, `InvoiceItem`, `Client`, `Address`, `Discount` | držia faktúru; settery overujú čísla a zľavy |
| Kódovanie | `Serializer` | z `Invoice` vytvorí telo JSON `{"invoice": {...}}` |
| Klient API | `InvoiceApi` | zostaví URL, zavolá transport, loguje, zabalí odpoveď do objektu odpovede |
| Transport | `Transport\GuzzleTransport`, `TestTransport`, `NullTransport` alebo vlastný | pošle jednu HTTP požiadavku a vráti jej stav a telo ako `Result` |
| Odpoveď | `Response\CreateInvoiceResponse`, `InvoiceDetailResponse`, `DeleteInvoiceResponse` | spracujú telo JSON, rozhodnú o `isOk()`, sprístupnia chybovú správu a dáta |

## Jedno volanie krok za krokom {#a-call-step-by-step}

Vezmime `$api->createInvoice($invoice)`:

1. **Zakódovanie.** `Serializer::encodeInvoice()` prevedie faktúru do JSON. Hodnoty sa zapíšu presne tak, ako ste ich nastavili. Text, ktorý nie je platné UTF-8, vyhodí `SerializationException`; nič sa neodošle ani nezaloguje.
2. **Zalogovanie požiadavky.** PSR-3 logger dostane `debug` `Invoice API request` s metódou a URL.
3. **Odoslanie.** Transport dostane metódu (`HttpMethod::Post`), URL (`{target}/api/v1/invoices/create`), token a JSON. `GuzzleTransport` ich pošle s hlavičkami `Authorization: Bearer <token>` a `Content-Type: application/json` a **každú** HTTP odpoveď, vrátane 4xx a 5xx, vráti ako `Result`.
4. **Žiadna HTTP odpoveď?** Zlyhanie DNS, odmietnuté spojenie, timeout alebo chyba TLS: transport vyhodí `TransportException`, logger dostane `error` `Invoice API request failed` a výnimka sa dostane k vášmu kódu.
5. **Spracovanie.** Objekt odpovede dekóduje telo. Odpoveď je OK, keď je HTTP stav 2xx a JSON obsahuje `"status": "ok"`. Inak je chybovou správou `message` z API alebo správa, ktorú napíše sám klient (pozrite [Odpovede](./responses.md#error-messages)).
6. **Zalogovanie výsledku.** Pri úspechu `info` `Invoice API response` so stavovým kódom; inak `warning` `Invoice API error: {error}`.
7. **Návrat.** Dostanete objekt odpovede a podľa `isOk()` sa rozhodnete, čo ďalej.

`getInvoice($id)` a `deleteInvoice($id)` krok 1 vynechajú: pošlú `GET {target}/api/v1/invoices/detail/{id}` a `DELETE {target}/api/v1/invoices/delete/{id}` bez tela. Id sa zakóduje do URL cez `rawurlencode()`, takže z `2026/002` je `2026%2F002` a zostane jedným segmentom cesty.

## Prečo chyby API nie sú výnimky {#why-api-errors-are-not-exceptions}

Neplatný token, chyba validácie aj chyba servera sú odpovede API. Vrátia sa ako odpoveď, aby ste mali vždy HTTP stav, správu a surové telo pre log alebo tiket na podporu, a jedno `if ($response->isOk())` pokryje všetky. Výnimky sú vyhradené pre zlyhania, pri ktorých niet čo čítať: žiadna HTTP odpoveď, faktúra, ktorú nemožno zakódovať, alebo hodnota, ktorá je zlá ešte pred odoslaním. Všetky ich vymenúva [Spracovanie chýb](./errors.md).

## Čo klient nerobí {#what-the-client-does-not-do}

- **Žiadne opakovanie.** Neúspešná požiadavka sa ohlási raz. Opakovanie pre `GET`/`DELETE` pridáte cez Guzzle middleware, pozrite [príklad s opakovaním](../examples/retries-and-timeouts.md). `createInvoice()` naslepo neopakujte: požiadavka, ktorá skončila timeoutom, už mohla faktúru vytvoriť.
- **Žiadna validácia obsahu faktúry.** Povinné polia, sadzby DPH a súčty kontroluje API, ktoré odpovie chybovou správou.
- **Žiadne údaje dodávateľa.** Dodávateľom faktúry je účet, ku ktorému patrí token; `Issuer` nemá žiadne polia a neodosiela sa.
