<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

interface ChangeStockControl
{
    public function getAvailableCoins(): array;

    public function addCoins(Coin $coin, int $quantity): void;

    public function removeCoins(Coin $coin, int $quantity): void;

    public function getStockOfCoin(Coin $coin): int;

    public function hasEnoughChange(float $credit): bool;
}
