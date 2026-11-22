<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/docs/snippets',
    ])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    ->withSkip([
        // "@return $this" documents the fluent contract of the interfaces.
        RemoveUselessReturnTagRector::class,
        // Issuer::fromArray() keeps the $data parameter of every other fromArray() until the issuer fields are mapped
        RemoveUnusedPublicMethodParameterRector::class => [__DIR__ . '/src/Invoice/Issuer.php'],
    ]);
