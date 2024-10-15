<?php declare(strict_types=1);

namespace VendingMachine\Common\Infrastructure\Outbound\Persistence;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinStock;

class InMemoryChangeStockControl implements ChangeStockControl
{
    private array $coins;

    public function __construct(array $coins = [])
    {
        $this->coins = empty($coins) ? [
            '1' => new CoinStock('1', new Coin(1.00), 10),
            '2' => new CoinStock('2', new Coin(0.25), 20),
            '3' => new CoinStock('3', new Coin(0.10), 25),
            '4' => new CoinStock('4', new Coin(0.05), 50),
        ] : $coins;
    }

    public function getAvailableCoins(): array
    {
        return $this->coins;
    }

    public function getTotalCoins(): int
    {
        return array_sum(array_map(fn (CoinStock $coinStock) => $coinStock->getQuantity(), $this->coins));
    }

    public function addCoins(Coin $coin, int $quantity): void
    {
        $coinStock = $this->searchCoin($coin);

        if ($coinStock === null) {
            return;
        }

        $coinStock->add($quantity);

        $this->coins[$coinStock->getId()] = $coinStock;
    }

    public function hasEnoughChange(float $credit): bool
    {
        $creditInCents = (int) round($credit * 100);
        foreach ($this->coins as $coinStock) {
            $creditInCents = $this->calculateRemainingCredit($creditInCents, $coinStock);
        }

        return $creditInCents === 0;
    }

    private function calculateRemainingCredit(int $credit, CoinStock $coinStock): int
    {
        $coin = $coinStock->getCoin();
        $coinValue = (int) round($coin->getAmount() * 100);

        while ($coinStock->getQuantity() > 0 && $credit >= $coinValue) {
            $credit -= $coinValue;
        }

        return $credit;
    }

    public function removeCoins(Coin $coin, int $quantity): void
    {
        $coinStock = $this->searchCoin($coin);
        if ($coinStock === null) {
            return;
        }

        $coinStock->remove($quantity);

        $this->coins[$coinStock->getId()] = $coinStock;
    }

    private function searchCoin(Coin $coin): ?CoinStock
    {
        $filtered = array_filter(
            $this->coins,
            fn (CoinStock $coinStock) => $coin->equals($coinStock->getCoin())
        );

        if (empty($filtered)) {
            return null;
        }

        return reset($filtered);
    }
}
