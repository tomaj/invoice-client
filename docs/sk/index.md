---
layout: home
title: Invoice Client – PHP klient pre API chcemfakturu.sk
titleTemplate: false
description: 'PHP klient pre fakturačné API chcemfakturu.sk: zostavte typované faktúry, vytvorte, načítajte a zmažte ich, každú chybu dostanete ako odpoveď, logujte cez PSR-3 a testujte bez siete.'
hero:
  name: Invoice Client
  text: Faktúry chcemfakturu.sk z PHP
  tagline: 'Zostavte faktúru z typovaných objektov, odošlite ju jedným volaním a dostanete odpoveď, ktorú viete skontrolovať: id, odkaz na PDF alebo presnú chybu. A to všetko otestujete bez siete.'
  image:
    light: /illustrations/hero-sk-light.svg
    dark: /illustrations/hero-sk-dark.svg
    alt: Faktúra s tromi položkami a pečiatkou UHRADENÉ, odoslaná ako JSON do fakturačného API, ktoré odpovie stavom ok
  actions:
    - theme: brand
      text: Začíname
      link: /sk/guide/getting-started
    - theme: alt
      text: Príklady
      link: /sk/examples/
    - theme: alt
      text: GitHub
      link: https://github.com/tomaj/invoice-client
features:
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="7" y="4" width="26" height="36" rx="5"/><path class="ic-muted" d="M13 13h14M13 20h14M13 27h8"/><circle class="ic-accf" cx="34" cy="34" r="10"/><path class="ic-mark" d="M29.5 34.3l3 3 6-6.3"/></svg>'
    title: Typované faktúry
    details: Invoice, InvoiceItem, Client, Address a Discount s fluent settermi alebo cez fromArray(). Currency a DiscountType sú enumy; zlá hodnota vyhodí výnimku skôr, než sa čokoľvek odošle.
    link: /sk/guide/invoices
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path class="ic-ink" d="M15 8c-5 0-5 3-5 7s-2 7-5 9c3 2 5 5 5 9s0 7 5 7M33 8c5 0 5 3 5 7s2 7 5 9c-3 2-5 5-5 9s0 7-5 7"/><path class="ic-acc" d="M19 24h.01M24 24h.01M29 24h.01"/></svg>'
    title: Presné dáta
    details: 'Ceny a kódy idú do API presne tak, ako ich zadáte: "40.00" zostane "40.00" a konštantný symbol si ponechá úvodnú nulu.'
    link: /sk/guide/invoices#values-are-sent-as-given
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path class="ic-paper" d="M6 10a5 5 0 0 1 5-5h26a5 5 0 0 1 5 5v18a5 5 0 0 1-5 5H20l-9 8v-8a5 5 0 0 1-5-5z"/><path class="ic-acc" d="M24 12v9"/><path class="ic-acc" d="M24 26.5h.01"/></svg>'
    title: Chyby sú odpovede
    details: Každé volanie vráti odpoveď s isOk(), HTTP stavom, správou z API a surovým telom. Výnimku vyhodí len požiadavka, na ktorú neprišla vôbec žiadna HTTP odpoveď.
    link: /sk/guide/errors
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="6" y="6" width="36" height="36" rx="6"/><path class="ic-acc" d="M12 16l3 3 5-5M12 30l3 3 5-5"/><path class="ic-muted" d="M25 17h11M25 31h11"/></svg>'
    title: Testovateľný
    details: TestTransport radí odpovede do fronty a zaznamenáva požiadavky; NullTransport nič neodosiela; GuzzleTransport prijme vlastného Guzzle klienta s MockHandlerom, opakovaním alebo timeoutmi.
    link: /sk/guide/testing
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="5" y="6" width="30" height="32" rx="5"/><path class="ic-muted" d="M11 14h18M11 21h18M11 28h10"/><rect class="ic-accf" x="27" y="27" width="16" height="14" rx="3"/><path class="ic-acc" d="M30 27v-3a5 5 0 0 1 10 0v3"/><path class="ic-mark" d="M35 32v4"/></svg>'
    title: Logovanie cez PSR-3, bez tajomstiev
    details: Odovzdajte ľubovoľný PSR-3 logger a uvidíte požiadavky, stavové kódy a chyby API. API token ani údaje faktúry sa do logu nikdy nedostanú.
    link: /sk/guide/logging
  - icon: '<svg class="ic-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><rect class="ic-paper" x="4" y="8" width="40" height="32" rx="5"/><path class="ic-ink" d="M4 16h40"/><path class="ic-acc" d="M11 24l5 4-5 4M20 32h9"/></svg>'
    title: Príklady, ktoré bežia
    details: Šesť receptov komunikuje s lokálnym simulovaným API spusteným cez php -S a CI každý z nich spustí proti aktuálnemu kódu pri každom pushi.
    link: /sk/examples/
---

## Ako to vyzerá

<<< @/snippets/getting-started.php{php}

Každé volanie prejde tými istými krokmi; chyba API sa vráti ako odpoveď, nikdy nie ako výnimka:

<Diagram name="flow" alt="Váš kód zostaví Invoice a zavolá InvoiceApi, ktoré ju serializuje do JSON a odovzdá transportu; transport ju pošle do API chcemfakturu.sk s bearer tokenom a vráti HTTP stav a telo ako Result; InvoiceApi z neho vytvorí objekt odpovede. Chýbajúca HTTP odpoveď vyhodí TransportException; neplatný vstup vyhodí výnimku ešte pred požiadavkou." />

## Kam ďalej

<LinkCards :items="[
  { title: 'Začíname', text: 'Nainštalujte balík a vytvorte svoju prvú faktúru.', link: '/sk/guide/getting-started' },
  { title: 'Zostavenie faktúry', text: 'Každé pole faktúry, jej položky, odberateľ, adresy a zľavy.', link: '/sk/guide/invoices' },
  { title: 'Spracovanie chýb', text: 'Ktoré zlyhania sú odpovede a ktoré výnimky.', link: '/sk/guide/errors' },
  { title: 'Príklady', text: 'Šesť spustiteľných receptov proti lokálnemu simulovanému API, otestovaných v CI.', link: '/sk/examples/' },
  { title: 'Testovanie integrácie', text: 'TestTransport, NullTransport a Guzzle MockHandler.', link: '/sk/guide/testing' },
  { title: 'Referencia API', text: 'Každá verejná trieda a metóda.', link: '/sk/api/reference' },
]" />
