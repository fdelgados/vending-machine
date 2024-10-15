<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\ReplenishChange;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\Quantity;

final readonly class ReplenishChangeService
{
    public function __construct(private ChangeStockControl $changeStockControl)
    {
    }

    public function replenish(ReplenishChangeCommand $command): void
    {
        $coin = new Coin($command->getCoinValue());
        $quantity = new Quantity($command->getQuantity());

        $this->changeStockControl->addCoins($coin, $quantity->getValue());
    }
}
