<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\ViewChange;

final readonly class CoinDto
{
    public function __construct(private string $id, private float $value, private int $quantity)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
