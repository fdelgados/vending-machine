<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Service;

use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Sale\CoinStock;
use VendingMachine\Operation\Domain\Model\Sale\Credit;

abstract class ChangeCalculator
{
    final public function calculate(Credit $credit): Result
    {
        $availableCoins = $this->getAvailableCoins();
        $change = [];

        foreach ($availableCoins as $coinStock) {
            $credit = $this->updateCredit($credit, $coinStock, $change);
        }

        if ($credit->isPositive()) {
            return Result::failure(Errors::notEnoughChange());
        }

        return Result::success($change);
    }

    private function updateCredit(Credit $credit, CoinStock $coinStock, array &$change): Credit
    {
        $coin = $coinStock->getCoin();
        while ($coinStock->getQuantity() > 0 && $credit->getAmount() >= $coin->getValue()) {
            $credit = $credit->subtract($coin->getValue());
            $coinStock->remove(1);
            $change[] = $coin;
        }

        return $credit;
    }

    /** @return CoinStock[] */
    abstract protected function getAvailableCoins(): array;
}
