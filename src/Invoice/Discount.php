<?php

declare(strict_types=1);

namespace Invoice;

class Discount
{
    use ArrayReader;

    private string $type;

    /** @var int|float|numeric-string */
    private int|float|string $value;

    /**
     * @param int|float|string $value a number or a numeric string, sent to the API as given
     * @throws \InvalidArgumentException for a non-numeric string
     */
    public function __construct(string $type, int|float|string $value)
    {
        $this->type = $type;
        $this->value = self::numeric($value, 'value') ?? 0;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): Discount
    {
        return new Discount(
            self::readString($data, 'type') ?? 'none',
            (float) (self::readNumber($data, 'value') ?? 0),
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Discount
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|float|numeric-string
     */
    public function getValue(): int|float|string
    {
        return $this->value;
    }

    /**
     * @param int|float|string $value a number or a numeric string, sent to the API as given
     * @throws \InvalidArgumentException for a non-numeric string
     */
    public function setValue(int|float|string $value): Discount
    {
        $this->value = self::numeric($value, 'value') ?? 0;
        return $this;
    }
}
