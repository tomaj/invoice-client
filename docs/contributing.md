# Contributing

Contributions are welcome. The full rules are in [CONTRIBUTING.md](https://github.com/tomaj/invoice-client/blob/master/CONTRIBUTING.md); this project follows the [Code of Conduct](https://github.com/tomaj/invoice-client/blob/master/CODE_OF_CONDUCT.md).

## Reporting bugs

Open an [issue](https://github.com/tomaj/invoice-client/issues/new/choose) with the smallest code that shows the problem and the expected and actual values. Never post your API token or real customer data.

Security vulnerabilities are reported in private: see the [security policy](https://github.com/tomaj/invoice-client/blob/master/SECURITY.md).

## Development

invoice-client supports PHP 8.4, 8.5 and 8.6.

```bash
git clone https://github.com/tomaj/invoice-client.git
cd invoice-client
composer install
composer check      # code style, PHPStan, Rector, tests
composer infection  # mutation testing (needs Xdebug or PCOV)
```

Every behaviour change needs a test and a `CHANGELOG.md` entry. The tests never call the real API: they use `TestTransport` or a Guzzle `MockHandler`.

## Documentation

This site is built with [VitePress](https://vitepress.dev/) from the [`docs/`](https://github.com/tomaj/invoice-client/tree/master/docs) folder. English pages are at the root of `docs/`, Slovak ones in `docs/sk/` with the same paths; a CI check (`npm run docs:locales`) fails when a page has no twin in the other language. Code on the pages comes from three places, all checked in CI:

- **`docs/snippets/`**: short snippets included with `<<< @/snippets/…`. `DocsSnippetsTest` runs every snippet that does not call the real API and compares its output with the `.out` file next to it; PHPStan analyses all of them.
- **`examples/`**: the [runnable examples](./examples/), included with `<<< @/../examples/…`. The `Examples` workflow runs each one against a local fake API and diffs its output with `expected-output.txt` (`examples/ci/recipe.sh <name>` locally).
- **Inline PHP code blocks** in `README.md`, `UPGRADE-1.0.md` and `docs/` (`docs/sk/` included): `DocsSnippetsTest` checks that each one is valid PHP. A block that is deliberately not valid PHP on its own (a method signature) is marked with `<!-- snippet: skip -->` on the line before it.

Images: the logo, favicon, illustrations and diagrams are hand-written SVG in `docs/public/` (light and dark variants). The Slovak variants of the illustrations and diagrams (`*-sk-light.svg`, `*-sk-dark.svg`) are generated from the English ones by `npm run illustrations` (`docs/scripts/illustrations.py`); `npm run docs:links` fails when they are out of date. `npm run images` renders `og.png`, the PNG icons and `favicon.ico` from them with Chrome (`CHROME_PATH` if Chrome is not in the default place); commit the results.

```bash
cd docs
npm ci
npm run docs:dev        # local preview
npm run docs:links      # build, check SVGs and links
npm run illustrations   # rewrite the Slovak variants after changing an English illustration or diagram
npm run images          # re-render og.png and the PNG icons after changing the logo
```
