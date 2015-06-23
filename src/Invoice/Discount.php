<?php

namespace Invoice;

class Discount
{
    private $type;

    private $value;

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public static function fromArray($data)
    {
        $type = 'none';
        $value = 0;
        if (isset($data['type'])) {
            $type = $data['type'];
        }
        if (isset($data['value'])) {
            $value = floatval($data['value']);
        }
        return new Discount($type, $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Discount
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return Discount
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
