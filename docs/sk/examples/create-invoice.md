---
description: Zostavte kompletnú faktúru s odberateľom, adresami, položkami a zľavami, vytvorte ju cez API a prečítajte id a odkazy.
---

# Príklad: vytvorenie faktúry

<ProjectSource name="create-invoice" :files="['run.php', 'expected-output.txt', 'e2e.sh']" />

Celá cesta skutočnej faktúry: odberateľ s IČO, DIČ, IČ DPH a adresou, dodacia adresa, dve položky (jedna s pevnou zľavou), 5 % zľava na celú faktúru a platobné symboly. Príklad ju vytvorí, vypíše odpoveď a odkazy a potom ju načíta späť, aby ukázal JSON, ktorý API dostalo.

## Kód {#the-code}

<<< @/../examples/create-invoice/run.php

## Výstup {#output}

<<< @/../examples/create-invoice/expected-output.txt{text}

## Na čo si všimnúť {#what-to-notice}

- **Hodnoty sa posielajú tak, ako ich zadáte.** `'40.00'` zostane reťazcom `"40.00"`, `120` zostane číslom a konštantný symbol `'0308'` si ponechá úvodnú nulu, pretože je to reťazec. Nenastavené polia ako `status` a `price` sa pošlú ako `null`.
- **Zľavy.** Pevná zľava je na položke s licenciou; celá faktúra dostane percentuálnu zľavu. Pevná zľava na celú faktúru by vyhodila `UnsupportedDiscountException`.
- **Dve cesty k odkazom.** `getHtmlUrl()` a `getDownloadUrl()` vracajú odkazy, ktoré poslalo API; `htmlUrl()`, `pdfUrl()` a `pdfDownloadUrl()` ich zostavia z id, ktoré máte uložené, bez požiadavky.
- **Token.** Simulované API prijme len `test-token`. V produkcii čítajte token z prostredia a `target` vynechajte.

Každé pole popisuje [Zostavenie faktúry](../guide/invoices.md), gettery odpovede [Odpovede](../guide/responses.md#createinvoiceresponse).

## Ako sa testuje {#how-it-is-tested}

<<< @/../examples/create-invoice/e2e.sh{bash}

`fake_api` spustí `examples/fake-api/server.php` na porte 8701 s prázdnym stavom, takže prvá faktúra je vždy `inv-0001`; `expect_output` porovná výstup s `expected-output.txt`.
