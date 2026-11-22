<?php

declare(strict_types=1);

namespace Invoice;

/**
 * Reads typed values from the loosely typed arrays given to fromArray().
 *
 * @internal
 */
trait ArrayReader
{
    /**
     * @param array<mixed> $data
     */
    private static function readString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return null;
    }

    /**
     * @param array<mixed> $data
     */
    private static function readNumber(array $data, string $key): int|float|null
    {
        $value = $data[$key] ?? null;
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return $value + 0;
        }

        return null;
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>|null
     */
    private static function readArray(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;

        return is_array($value) ? $value : null;
    }
}
