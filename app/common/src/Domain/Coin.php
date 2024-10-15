<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

final class Coin implements Money
{
    private const float EPSILON = 0.00001;
    private const array ALLOWED_VALUES = [0.05, 0.10, 0.25, 1.0];

    private float $value;

    public function __construct(float $value)
    {
        ensure(in_array($value, self::ALLOWED_VALUES), 'Invalid coin value');

        $this->value = $value;
    }

    public function equals(Coin $other): bool
    {
        return abs($this->value - $other->value) < self::EPSILON;
    }

    public function getAmount(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return number_format($this->value, 2);
    }
}
