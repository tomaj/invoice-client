---
description: Prechod z neotagovaného dev-master invoice-client na 1.0 - menný priestor, konštruktor, odpovede, enumy, transporty a výnimky s kódom pred a po.
---

# Prechod z dev-master na 1.0

Táto stránka zobrazuje [UPGRADE-1.0.md](https://github.com/tomaj/invoice-client/blob/master/UPGRADE-1.0.md) z vetvy `master`; návod sa vedie po anglicky a nižšie je bez prekladu. invoice-client pred 1.0.0 nikdy nemal tag, takže návod je pre projekty, ktoré inštalovali `dev-master` (kód z roku 2018 na Packagiste alebo `master` počas roka 2026). Každá zmena je aj v [zozname zmien](./changelog.md).

V skratke: triedy sa presunuli do `Tomaj\InvoiceClient\`, konštruktor `InvoiceApi` namiesto poľa volieb prijíma pomenované argumenty, meny a typy zliav sú enumy a chyby API sa vracajú ako odpovede, nie ako výnimky.

<!--@include: ../../UPGRADE-1.0.md{4,}-->
