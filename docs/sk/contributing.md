---
description: Ako prispieť do invoice-client - hlásenie chýb, vývoj, testy a dokumentácia v angličtine a slovenčine.
---

# Prispievanie

Príspevky sú vítané. Úplné pravidlá sú v [CONTRIBUTING.md](https://github.com/tomaj/invoice-client/blob/master/CONTRIBUTING.md); projekt sa riadi [kódexom správania](https://github.com/tomaj/invoice-client/blob/master/CODE_OF_CONDUCT.md).

## Hlásenie chýb {#reporting-bugs}

Založte [issue](https://github.com/tomaj/invoice-client/issues/new/choose) s čo najmenším kódom, na ktorom je problém vidieť, a s očakávanými a skutočnými hodnotami. Nikdy nezverejňujte svoj API token ani skutočné údaje odberateľov.

Bezpečnostné zraniteľnosti sa hlásia súkromne: pozrite si [bezpečnostnú politiku](https://github.com/tomaj/invoice-client/blob/master/SECURITY.md).

## Vývoj {#development}

invoice-client podporuje PHP 8.4, 8.5 a 8.6.

```bash
git clone https://github.com/tomaj/invoice-client.git
cd invoice-client
composer install
composer check      # code style, PHPStan, Rector, testy
composer infection  # mutačné testovanie (potrebuje Xdebug alebo PCOV)
```

Každá zmena správania potrebuje test a záznam v `CHANGELOG.md`. Testy nikdy nevolajú skutočné API: používajú `TestTransport` alebo Guzzle `MockHandler`.

## Dokumentácia {#documentation}

Tento web sa zostavuje cez [VitePress](https://vitepress.dev/) z priečinka [`docs/`](https://github.com/tomaj/invoice-client/tree/master/docs). Anglické stránky sú v koreni `docs/`, slovenské v `docs/sk/` s rovnakými cestami; kontrola v CI (`npm run docs:locales`) zlyhá, keď stránke chýba dvojča v druhom jazyku. Kód na stránkach pochádza z troch miest a CI kontroluje všetky:

- **`docs/snippets/`**: krátke ukážky vkladané cez `<<< @/snippets/…`. `DocsSnippetsTest` spustí každú ukážku, ktorá nevolá skutočné API, a jej výstup porovná so súborom `.out` vedľa nej; PHPStan analyzuje všetky.
- **`examples/`**: [spustiteľné príklady](./examples/), vkladané cez `<<< @/../examples/…`. Workflow `Examples` spustí každý z nich proti lokálnemu simulovanému API a jeho výstup porovná s `expected-output.txt` (lokálne `examples/ci/recipe.sh <name>`).
- **Inline PHP bloky** v `README.md`, `UPGRADE-1.0.md` a `docs/` (aj `docs/sk/`): `DocsSnippetsTest` overí, že každý je platné PHP. Blok, ktorý samostatne zámerne nie je platné PHP (signatúra metódy), sa označí `<!-- snippet: skip -->` na riadku pred ním.

Obrázky: logo, favicon, ilustrácie a diagramy sú ručne písané SVG v `docs/public/` (svetlá a tmavá varianta). Slovenské varianty ilustrácií a diagramov (`*-sk-light.svg`, `*-sk-dark.svg`) vygeneruje z anglických `npm run illustrations` (`docs/scripts/illustrations.py`); `npm run docs:links` zlyhá, keď sú zastarané. `npm run images` vyrenderuje z obrázkov cez Chrome `og.png`, PNG ikony a `favicon.ico` (`CHROME_PATH`, ak Chrome nie je na predvolenom mieste); výsledky commitnite.

```bash
cd docs
npm ci
npm run docs:dev        # lokálny náhľad
npm run docs:links      # build, kontrola SVG a odkazov
npm run illustrations   # po zmene anglickej ilustrácie alebo diagramu prepíše slovenské varianty
npm run images          # po zmene loga znova vyrenderuje og.png a PNG ikony
```
