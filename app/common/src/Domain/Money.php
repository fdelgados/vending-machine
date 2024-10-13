<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

abstract class Money implements \Stringable
{
    protected float $amount;

    public function plus(Money $addend): static
    {
        return new static($addend->amount + $this->amount);
    }

    public function minus(Money $subtrahend): static
    {
        return new static($this->amount - $subtrahend->amount);
    }

    public function equals(Money $money): bool
    {
        return $this->amount === $money->amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function isGreaterThan(Money $money): bool
    {
        return $this->amount > $money->amount;
    }

    public function isLessThan(Money $money): bool
    {
        return $this->amount < $money->amount;
    }

    public function __toString(): string
    {
        return number_format($this->amount, 2);
    }
}
