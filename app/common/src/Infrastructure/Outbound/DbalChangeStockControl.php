<?php declare(strict_types=1);

namespace VendingMachine\Common\Infrastructure\Outbound;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinStock;
use VendingMachine\Lib\Doctrine\DbalService;

final class DbalChangeStockControl extends DbalService implements ChangeStockControl
{
    public function getTotalCoins(): int
    {
        $result = $this->connection
            ->executeQuery('SELECT SUM(quantity) FROM change_stock')->fetchOne();

        return (int) $result;
    }

    public function getAvailableCoins(): array
    {
        $availableCoins = [];

        $rows = $this->getQueryBuilder()->select('*')
            ->from('change_stock')
            ->orderBy('value', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $availableCoins[] = new CoinStock(
                new Coin((float) $row['value']),
                (int) $row['quantity']
            );
        }

        return $availableCoins;
    }

    public function addCoins(Coin $coin, int $quantity): void
    {
        $sql = 'UPDATE change_stock SET quantity = quantity + :quantity WHERE value = :value';

        $this->connection->executeStatement($sql, [
            'quantity' => $quantity,
            'value' => $coin->__toString(),
        ]);
    }

    public function getStockOfCoin(Coin $coin): int
    {
        $result = $this->getQueryBuilder()->select('quantity')
            ->from('change_stock')
            ->where('value = :value')
            ->setParameter('value', $coin->__toString())
            ->fetchOne();

        return (int) $result;
    }

    public function removeCoins(Coin $coin, int $quantity): void
    {
        $sql = 'UPDATE change_stock SET quantity = quantity - :quantity WHERE value = :value';

        $this->connection->executeStatement($sql, [
            'quantity' => $quantity,
            'value' => $coin->__toString(),
        ]);
    }

    public function hasEnoughChange(float $credit): bool
    {
        $coins = $this->getAvailableCoins();
        $creditInCents = (int) round($credit * 100);
        foreach ($coins as $coinStock) {
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
}
