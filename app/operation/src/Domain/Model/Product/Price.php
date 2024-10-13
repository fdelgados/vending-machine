<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Product;

use VendingMachine\Common\Domain\Money;

final class Price implements Money
{
    private float $amount;

    public function __construct(float $amount)
    {
        ensure($amount > 0.0, 'Price must be greater than 0');

        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function __toString(): string
    {
        return number_format($this->amount, 2);
    }
}
