<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\ViewChange;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\CoinStock;

final readonly class ViewChangeService
{
    public function __construct(private ChangeStockControl $changeStockControl)
    {
    }

    public function view(): CoinMap
    {
        $availableCoins = $this->changeStockControl->getAvailableCoins();

        return $this->assemble($availableCoins);
    }

    private function assemble(array $availableCoins): CoinMap
    {
        $coinMap = new CoinMap();

        /** @var CoinStock $coin */
        foreach ($availableCoins as $coin) {
            $coinMap->add(
                $coin->getId(),
                new CoinDto(
                    $coin->getId(),
                    $coin->getCoinValue(),
                    $coin->getQuantity()
                )
            );
        }

        return $coinMap;
    }
}
