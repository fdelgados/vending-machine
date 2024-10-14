<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

interface ChangeStockControl
{
    const COINS = [
        '0.05' => 100,
        '0.10' => 50,
        '0.25' => 20,
        '1.00' => 10,
    ];

    public function getAvailableCoins(): array;

    public function addCoins(Coin $coin, int $quantity): void;

    public function removeCoins(Coin $coin, int $quantity): void;

    public function getStockOfCoin(Coin $coin): int;

    public function hasEnoughChange(float $credit): bool;
}
