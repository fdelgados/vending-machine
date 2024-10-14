<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

interface ChangeDispenser
{
    public function getAvailableCoins(): array;

    public function addCoins(Coin $coin, int $quantity): void;

    public function removeCoins(Coin $coin, int $quantity): void;

    public function getStockOfCoin(Coin $coin): int;
}
