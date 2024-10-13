<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Sale;

use VendingMachine\Common\Domain\Money;

final class Coin implements Money
{
    private const array ALLOWED_VALUES = [0.05, 0.10, 0.25, 1.0];

    private float $value;

    public function __construct(float $value)
    {
        ensure(in_array($value, self::ALLOWED_VALUES), 'Invalid coin value');

        $this->value = $value;
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
