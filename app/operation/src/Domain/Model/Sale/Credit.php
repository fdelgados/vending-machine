<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Sale;

use VendingMachine\Common\Domain\Money;

final class Credit extends Money
{
    public function __construct(float $amount)
    {
        ensure($amount >= 0.0, 'Credit must be greater or equal to 0.0');

        $this->amount = $amount;
    }

    public function sum(float $amount): self
    {
        return new self($this->amount + $amount);
    }

    public function subtract(float $amount): self
    {
        return new self(round($this->amount - $amount, 2));
    }

    public function isPositive(): bool
    {
        return $this->amount > 0.0;
    }

    public static function zero(): self
    {
        return new self(0.0);
    }
}
