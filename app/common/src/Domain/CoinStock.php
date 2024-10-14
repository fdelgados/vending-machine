<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

final class CoinStock
{
    private string $id;
    private Coin $coin;
    private int $quantity;

    public function __construct(Coin $coin, int $quantity)
    {
        ensure($quantity >= 0, 'Quantity must be greater than 0');

        $this->id = (string) $coin;
        $this->coin = $coin;
        $this->quantity = $quantity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCoin(): Coin
    {
        return $this->coin;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function add(int $quantity): void
    {
        ensure($quantity > 0, 'Quantity must be greater than 0');

        $this->quantity += $quantity;
    }

    public function remove(int $quantity): void
    {
        ensure($quantity > 0, 'Quantity must be greater than 0');
        ensure($this->quantity >= $quantity, 'Quantity must be less than or equal to the current quantity');

        $this->quantity -= $quantity;
    }
}
