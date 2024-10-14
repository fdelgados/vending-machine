<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\ReplenishChange;

final readonly class ReplenishChangeCommand
{
    public function __construct(private float $coinValue, private int $quantity)
    {
    }

    public function getCoinValue(): float
    {
        return $this->coinValue;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
