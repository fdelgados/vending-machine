<?php declare(strict_types=1);

namespace VendingMachine\Operation\Infrastructure\Outbound\ChangeCalculator;

use VendingMachine\Operation\Domain\Model\Sale\Coin;
use VendingMachine\Operation\Domain\Model\Sale\CoinStock;
use VendingMachine\Operation\Domain\Service\ChangeCalculator;

final class InMemoryChangeCalculator extends ChangeCalculator
{
    protected function getAvailableCoins(): array
    {
        return [
            '1.00' => new CoinStock(new Coin(1.00), 10),
            '0.25' => new CoinStock(new Coin(0.25), 20),
            '0.10' => new CoinStock(new Coin(0.10), 25),
            '0.05' => new CoinStock(new Coin(0.05), 50),
        ];
    }
}
