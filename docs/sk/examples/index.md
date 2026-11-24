---
description: Šesť spustiteľných receptov invoice-client proti lokálnemu simulovanému API chcemfakturu.sk, spúšťaných v CI pri každom pushi.
---

# Príklady

Kompletné, spustiteľné recepty. Každý je v [`examples/`](https://github.com/tomaj/invoice-client/tree/master/examples) ako malý Composer projekt s `run.php` (alebo PHPUnit testami) a `expected-output.txt`. Komunikujú so [simulovaným API chcemfakturu.sk](./fake-api.md), ktoré beží na vstavanom webovom serveri PHP, takže nepotrebujú sieť, účet ani token a nikdy nevytvoria skutočné faktúry.

[Workflow `Examples`](https://github.com/tomaj/invoice-client/blob/master/.github/workflows/examples.yml) pri každom pushi a pull requeste nainštaluje každý príklad proti aktuálnemu kódu, spustí ho a jeho výstup porovná s `expected-output.txt` riadok po riadku. Kód a výstup na týchto stránkach sa vkladajú priamo z týchto súborov, takže čítate presne to, čo spúšťa CI.

<LinkCards :items="[
  { title: 'Vytvorenie faktúry', text: 'Kompletná faktúra s odberateľom, adresami, položkami a zľavami; odpoveď, odkazy a čo API dostalo.', link: '/sk/examples/create-invoice' },
  { title: 'Životný cyklus faktúry', text: 'fromArray(), vytvorenie, načítanie, odkazy, zmazanie a následná 404.', link: '/sk/examples/invoice-lifecycle' },
  { title: 'Spracovanie chýb', text: 'Každá chybová odpoveď a každá výnimka balíka, vyvolané zámerne.', link: '/sk/examples/error-handling' },
  { title: 'Logovanie', text: 'Každý PSR-3 záznam úspešného volania, chyby API a zlyhania spojenia.', link: '/sk/examples/logging' },
  { title: 'Opakovanie a timeouty', text: 'Vlastný Guzzle klient: retry middleware pre GET a DELETE, timeout, ďalšia hlavička.', link: '/sk/examples/retries-and-timeouts' },
  { title: 'Testovanie s PHPUnit', text: 'Testy vašej vlastnej služby s TestTransport a Guzzle MockHandler.', link: '/sk/examples/testing' },
]" />

## Spustenie príkladu {#running-an-example}

Potrebujete PHP 8.4+, Composer a voľné porty 8701-8705. Z klonu repozitára:

```bash
examples/ci/recipe.sh create-invoice
```

`recipe.sh` skopíruje príklad do dočasného adresára, nainštaluje váš checkout ako `tomaj/invoice-client` cez Composer path repository, spustí simulované API, spustí príklad a porovná výstup. Po zámernej zmene výstupu `UPDATE_EXPECTED=1 examples/ci/recipe.sh <example>` prepíše `expected-output.txt`.

Vo vlastnom projekte skopírujte `run.php`, `'test-token'` nahraďte svojím tokenom z prostredia a vynechajte argument `target`, aby klient komunikoval s chcemfakturu.sk.
