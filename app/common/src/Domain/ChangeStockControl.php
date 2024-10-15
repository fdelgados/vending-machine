<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

interface ChangeStockControl
{
    public function getTotalCoins(): int;

    public function getAvailableCoins(): array;

    public function addCoins(Coin $coin, int $quantity): void;

    public function removeCoins(Coin $coin, int $quantity): void;

    public function hasEnoughChange(float $credit): bool;
}
