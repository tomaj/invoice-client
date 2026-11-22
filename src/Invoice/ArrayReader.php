<?php

declare(strict_types=1);

namespace Invoice;

/**
 * Reads typed values from the loosely typed arrays given to fromArray()
 * and validates numeric values given to setters.
 *
 * Values are kept as given (an int stays an int, "12.30" stays "12.30"),
 * so the JSON sent to the API matches what the caller passed in.
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
     * Reads a code or number that may be given as a string or an int (invoice number, variable symbol, zip, ...).
     *
     * @param array<mixed> $data
     */
    private static function readIdentifier(array $data, string $key): string|int|null
    {
        $value = $data[$key] ?? null;
        if (is_string($value) || is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            return (string) $value;
        }

        return null;
    }

    /**
     * @param array<mixed> $data
     * @return int|float|numeric-string|null
     */
    private static function readNumber(array $data, string $key): int|float|string|null
    {
        $value = $data[$key] ?? null;
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return $value;
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

    /**
     * @return int|float|numeric-string|null
     * @throws \InvalidArgumentException for a non-numeric string
     */
    private static function numeric(int|float|string|null $value, string $field): int|float|string|null
    {
        if (is_string($value) && !is_numeric($value)) {
            throw new \InvalidArgumentException(sprintf("%s must be a number or a numeric string, '%s' given", $field, $value));
        }

        return $value;
    }
}
