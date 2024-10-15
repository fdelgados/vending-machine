<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

final class Quantity
{
    private int $quantity;

    public function __construct(int $quantity)
    {
        ensure($quantity > 0, 'Quantity must be greater than 0');

        $this->quantity = $quantity;
    }

    public function getValue(): int
    {
        return $this->quantity;
    }
}
