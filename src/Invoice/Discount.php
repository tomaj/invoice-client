<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Invoice;

use Tomaj\InvoiceClient\Exception\InvalidValueException;

/**
 * A discount of an invoice item or of the whole invoice.
 */
final readonly class Discount
{
    use ArrayReader;

    /** @var int|float|numeric-string */
    private int|float|string $value;

    /**
     * @param int|float|string $value a number or a numeric string, sent to the API as given
     * @throws InvalidValueException for a non-numeric string
     */
    public function __construct(
        private DiscountType $type,
        int|float|string $value,
    ) {
        if (is_string($value) && !is_numeric($value)) {
            throw new InvalidValueException(sprintf("value must be a number or a numeric string, '%s' given", $value));
        }
        $this->value = $value;
    }

    /**
     * Reads "type" ("none", "percent" or "flat"; "none" when missing) and "value" (sent as a float).
     *
     * @param array<mixed> $data
     * @throws InvalidValueException for an unknown discount type
     */
    public static function fromArray(array $data): Discount
    {
        $type = self::readString($data, 'type') ?? DiscountType::None->value;

        return new Discount(
            DiscountType::tryFrom($type) ?? throw new InvalidValueException(sprintf("Unknown discount type '%s'", $type)),
            (float) (self::readNumber($data, 'value') ?? 0),
        );
    }

    public function getType(): DiscountType
    {
        return $this->type;
    }

    /**
     * @return int|float|numeric-string
     */
    public function getValue(): int|float|string
    {
        return $this->value;
    }
}
