#!/usr/bin/env python3
"""Write the Slovak variants of the hand-written SVG illustrations and diagrams.

The English SVGs in docs/public/illustrations/ and docs/public/diagrams/ are drawn by hand in a
light and a dark variant. Their Slovak twins (<name>-sk-light.svg, <name>-sk-dark.svg) are
generated from them by replacing the English labels below, so a change to a drawing reaches both
languages. Run after changing a drawing and commit the generated files:

    python3 docs/scripts/illustrations.py           # write the -sk-*.svg files
    python3 docs/scripts/illustrations.py --check   # fail when a generated file is out of date

A replacement whose English text is no longer in the drawing is an error, so a changed label
cannot silently stay English in the Slovak variant.
"""

from __future__ import annotations

import sys
from pathlib import Path

PUBLIC = Path(__file__).resolve().parents[1] / "public"

# Exact substrings of the English SVG -> their Slovak replacement. Labels are matched with the
# surrounding `>`/`<` so a word inside a longer label or an attribute is never replaced by accident.
STRINGS: dict[str, list[tuple[str, str]]] = {
    "illustrations/hero": [
        ('aria-label="An invoice with three items and a paid stamp, sent as JSON to the invoicing API, which answers with status ok"',
         'aria-label="Faktúra s tromi položkami a pečiatkou UHRADENÉ, odoslaná ako JSON do fakturačného API, ktoré odpovie stavom ok"'),
        (">Invoice 2026001<", ">Faktúra 2026001<"),
        (">DUE 2026-12-08 · VS 2026001<", ">SPLATNÁ 8. 12. 2026 · VS 2026001<"),
        (">CLIENT<", ">ODBERATEĽ<"),
        (">ITEM<", ">POLOŽKA<"),
        (">QTY<", ">MN.<"),
        (">PRICE<", ">CENA<"),
        (">Consulting<", ">Konzultácie<"),
        (">400.00<", ">400,00<"),
        (">Annual licence<", ">Ročná licencia<"),
        (">100.00<", ">100,00<"),
        (">Support<", ">Podpora<"),
        (">60.00<", ">60,00<"),
        (">Total<", ">Spolu<"),
        (">€ 560.00<", ">560,00 €<"),
        # the stamp: a longer word, so a wider frame and a smaller font
        ('<rect x="236" y="136" width="96" height="38" rx="9"', '<rect x="220" y="136" width="128" height="38" rx="9"'),
        ("font: 800 20px ui-sans-serif, system-ui, sans-serif; letter-spacing: .14em;", "font: 800 15px ui-sans-serif, system-ui, sans-serif; letter-spacing: .08em;"),
        (">PAID<", ">UHRADENÉ<"),
    ],
    "diagrams/flow": [
        ('aria-label="Your code builds an Invoice and calls InvoiceApi, which serializes it to JSON and hands it to a transport; the transport sends it to the chcemfakturu.sk API with the bearer token and returns the HTTP status and body as a Result; InvoiceApi wraps it in a response object. A missing HTTP response throws TransportException; invalid input throws before any request."',
         'aria-label="Váš kód zostaví Invoice a zavolá InvoiceApi, ktoré ju serializuje do JSON a odovzdá transportu; transport ju pošle do API chcemfakturu.sk s bearer tokenom a vráti HTTP stav a telo ako Result; InvoiceApi z neho vytvorí objekt odpovede. Chýbajúca HTTP odpoveď vyhodí TransportException; neplatný vstup vyhodí výnimku ešte pred požiadavkou."'),
        (">Your code<", ">Váš kód<"),
        (">JSON body<", ">telo JSON<"),
        (">HTTP response<", ">HTTP odpoveď<"),
        (">any body<", ">ľubovoľné telo<"),
        (">  ... else log the error<", ">  … inak zaloguj chybu<"),
        (">Exceptions: no response or bad input<", ">Výnimky: bez odpovede alebo zlý vstup<"),
        (">no HTTP response at all<", ">žiadna HTTP odpoveď<"),
        (">invoice is not valid JSON<", ">faktúra nie je platný JSON<"),
        (">wrong values, thrown early<", ">zlé hodnoty, hneď na začiatku<"),
        (">debug request, info response,<", ">debug požiadavka, info odpoveď,<"),
        (">warning API error, error failure;<", ">warning chyba API, error zlyhanie;<"),
        (">never the token or invoice data<", ">nikdy token ani údaje faktúry<"),
    ],
}

VARIANTS = ("light", "dark")


def translate(name: str, variant: str) -> tuple[Path, str]:
    source = PUBLIC / f"{name}-{variant}.svg"
    text = source.read_text(encoding="utf-8")
    for english, slovak in STRINGS[name]:
        if english not in text:
            raise SystemExit(f"{source}: {english!r} not found; update STRINGS in {Path(__file__).name}")
        text = text.replace(english, slovak)
    return PUBLIC / f"{name}-sk-{variant}.svg", text


def main(argv: list[str]) -> int:
    check = "--check" in argv[1:]
    stale: list[str] = []
    for name in STRINGS:
        for variant in VARIANTS:
            target, text = translate(name, variant)
            current = target.read_text(encoding="utf-8") if target.exists() else None
            if current == text:
                print(f"ok {target.relative_to(PUBLIC.parent)}")
                continue
            if check:
                stale.append(str(target.relative_to(PUBLIC.parent)))
                continue
            target.write_text(text, encoding="utf-8")
            print(f"wrote {target.relative_to(PUBLIC.parent)}")
    if stale:
        print("Out of date, run python3 docs/scripts/illustrations.py and commit the result:", file=sys.stderr)
        print("\n".join(stale), file=sys.stderr)
        return 1
    return 0


if __name__ == "__main__":
    sys.exit(main(sys.argv))
