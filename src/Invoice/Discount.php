<?php

declare(strict_types=1);

namespace Invoice;

class Discount
{
    use ArrayReader;

    private string $type;

    private int|float $value;

    public function __construct(string $type, int|float $value)
    {
        $this->type = $type;
        $this->value = $value;
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

    public function getValue(): int|float
    {
        return $this->value;
    }

    public function setValue(int|float $value): Discount
    {
        $this->value = $value;
        return $this;
    }
}
