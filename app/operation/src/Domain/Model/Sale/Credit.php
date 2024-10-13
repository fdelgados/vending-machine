<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Sale;

use VendingMachine\Common\Domain\Money;

final class Credit implements Money
{
    private const float EPSILON = 0.00001;

    private float $amount;

    public function __construct(float $amount)
    {
        ensure($amount >= 0.0, 'Credit must be greater or equal to 0.0');

        $this->amount = $amount;
    }

    public static function zero(): self
    {
        return new self(0.0);
    }

    public function isPositive(): bool
    {
        return $this->amount > self::EPSILON;
    }

    public function plus(Money $addend): Credit
    {
        return new Credit($this->round($addend->getAmount() + $this->amount));
    }

    public function minus(Money $subtrahend): Credit
    {
        return new Credit($this->round($this->amount - $subtrahend->getAmount()));
    }

    private function round(float $amount): float
    {
        return round($amount, 2);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function equals(Money $other): bool
    {
        return abs($this->amount - $other->getAmount()) < self::EPSILON;
    }

    public function isGreaterThan(Money $other): bool
    {
        return ($this->amount - $other->getAmount()) > self::EPSILON;
    }

    public function isGreaterOrEqual(Money $other): bool
    {
        return ($this->amount - $other->getAmount()) >= -self::EPSILON;
    }

    public function isLessThan(Money $other): bool
    {
        return ($this->amount - $other->getAmount()) < -self::EPSILON;
    }

    public function __toString(): string
    {
        return number_format($this->amount, 2);
    }
}
