<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The code on the documentation site is imported from docs/snippets. Snippets with a .out file are run
 * and must print exactly that; the others call the real API and are only parsed (PHPStan analyses all of them).
 *
 * Inline ```php blocks of README.md, UPGRADE-1.0.md and docs/ must be valid PHP. A block directly after
 * `<!-- snippet: skip -->` is not checked (a bare method signature, which is not valid PHP on its own).
 * The runnable examples in examples/ are checked by the Examples workflow.
 */
final class DocsSnippetsTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function snippets(): iterable
    {
        $files = glob(__DIR__ . '/../docs/snippets/*.php');
        self::assertNotFalse($files);
        foreach ($files as $file) {
            yield basename($file) => [$file];
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function inlineBlocks(): iterable
    {
        $root = dirname(__DIR__);
        $files = [$root . '/README.md', $root . '/UPGRADE-1.0.md'];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root . '/docs', \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            self::assertInstanceOf(\SplFileInfo::class, $file);
            $path = $file->getPathname();
            if ($file->getExtension() === 'md' && !str_contains($path, '/node_modules/') && !str_contains($path, '/.vitepress/')) {
                $files[] = $path;
            }
        }
        sort($files);

        foreach ($files as $path) {
            $lines = explode("\n", (string) file_get_contents($path));
            $previous = '';
            $count = count($lines);
            for ($i = 0; $i < $count; $i++) {
                if (preg_match('/^```php\b/', $lines[$i]) !== 1) {
                    if (trim($lines[$i]) !== '') {
                        $previous = trim($lines[$i]);
                    }
                    continue;
                }
                $start = $i + 1;
                $body = [];
                for ($i++; $i < $count && !str_starts_with($lines[$i], '```'); $i++) {
                    $body[] = $lines[$i];
                }
                if ($previous !== '<!-- snippet: skip -->') {
                    yield substr($path, strlen($root) + 1) . ':' . $start => [implode("\n", $body)];
                }
                $previous = '';
            }
        }
    }

    public function testDocumentationHasInlineBlocks(): void
    {
        self::assertGreaterThan(20, iterator_count(self::inlineBlocks()));
    }

    #[DataProvider('inlineBlocks')]
    public function testInlineBlockIsValidPhp(string $code): void
    {
        $source = str_starts_with(ltrim($code), '<?php') ? $code : "<?php\n" . $code;
        // TOKEN_PARSE throws ParseError on a syntax error
        self::assertNotSame([], token_get_all($source, TOKEN_PARSE));
    }

    #[DataProvider('snippets')]
    public function testSnippet(string $file): void
    {
        $code = file_get_contents($file);
        self::assertIsString($code);
        // TOKEN_PARSE throws ParseError on a syntax error
        self::assertNotSame([], token_get_all($code, TOKEN_PARSE));

        $expected = $file . '.out';
        if (!is_file($expected)) {
            self::assertStringContainsString("getenv('CHCEMFAKTURU_API_TOKEN')", $code, 'A snippet that does not call the real API needs a .out file with its output');
            return;
        }

        $this->expectOutputString((string) file_get_contents($expected));
        (static function (string $file): void {
            require $file;
        })($file);
    }
}
