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

This site is built with [VitePress](https://vitepress.dev/) from the [`docs/`](https://github.com/tomaj/invoice-client/tree/master/docs) folder. Code on the pages comes from three places, all checked in CI:

- **`docs/snippets/`**: short snippets included with `<<< @/snippets/…`. `DocsSnippetsTest` runs every snippet that does not call the real API and compares its output with the `.out` file next to it; PHPStan analyses all of them.
- **`examples/`**: the [runnable examples](./examples/), included with `<<< @/../examples/…`. The `Examples` workflow runs each one against a local fake API and diffs its output with `expected-output.txt` (`examples/ci/recipe.sh <name>` locally).
- **Inline PHP code blocks** in `README.md`, `UPGRADE-1.0.md` and `docs/`: `DocsSnippetsTest` checks that each one is valid PHP. A block that is deliberately not valid PHP on its own (a method signature) is marked with `<!-- snippet: skip -->` on the line before it.

Images: the logo, favicon, illustrations and diagrams are hand-written SVG in `docs/public/` (light and dark variants). `npm run images` renders `og.png`, the PNG icons and `favicon.ico` from them with Chrome (`CHROME_PATH` if Chrome is not in the default place); commit the results.

```bash
cd docs
npm ci
npm run docs:dev    # local preview
npm run docs:links  # build, check SVGs and links
npm run images      # re-render og.png and the PNG icons after changing the logo
```
