<?php declare(strict_types=1);

namespace VendingMachine\Common\Infrastructure\Outbound;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinStock;

class InMemoryChangeStockControl implements ChangeStockControl
{
    private array $coins;

    public function __construct(array $coins = [])
    {
        $this->coins = empty($coins) ? [
            '1.00' => new CoinStock(new Coin(1.00), 10),
            '0.25' => new CoinStock(new Coin(0.25), 20),
            '0.10' => new CoinStock(new Coin(0.10), 25),
            '0.05' => new CoinStock(new Coin(0.05), 50),
        ] : $coins;
    }

    public function getAvailableCoins(): array
    {
        return $this->coins;
    }

    public function getStockOfCoin(Coin $coin): int
    {
        return $this->coins[(string) $coin]->getQuantity();
    }

    public function addCoins(Coin $coin, int $quantity): void
    {
        $coinStock = $this->coins[(string) $coin] ?? new CoinStock($coin, 0);

        $coinStock->add($quantity);

        $this->coins[(string) $coin] = $coinStock;
    }

    public function removeCoins(Coin $coin, int $quantity): void
    {
        if (!isset($this->coins[(string) $coin])) {
            return;
        }

        $coinStock = $this->coins[(string) $coin];
        $coinStock->remove($quantity);

        $this->coins[(string) $coin] = $coinStock;
    }
}
