<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Service;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Common\Domain\CoinStock;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Sale\Credit;

readonly class ChangeDispenser
{
    public function __construct(private ChangeStockControl $changeStockControl)
    {
    }

    public function dispense(Credit $credit): Result
    {
        $change = new CoinCollection();
        $coins = $this->changeStockControl->getAvailableCoins();

        foreach ($coins as $coinStock) {
            $credit = $this->updateCredit($credit, $coinStock, $change);
        }

        if ($credit->isPositive()) {
            return Result::failure(Errors::notEnoughChange());
        }

        return Result::success($change);
    }

    private function updateCredit(Credit $credit, CoinStock $coinStock, CoinCollection $change): Credit
    {
        $coin = $coinStock->getCoin();
        while ($coinStock->getQuantity() > 0 && $credit->isGreaterOrEqual($coin)) {
            $credit = $credit->minus($coin);
            $coinStock->remove(1);
            $change->add($coin);
        }

        return $credit;
    }
}
