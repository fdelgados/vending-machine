<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;

final readonly class AddCreditService
{
    public function __construct(
        private SaleRepository $saleRepository,
        private ChangeStockControl $changeStockControl
    ) {
    }

    public function add(AddCreditCommand $command): AddCreditResult
    {
        $amounts = array_map(fn (float $coinValue) => new Coin($coinValue), $command->getCoinValues());
        $coins = new CoinCollection(...$amounts);

        $sale = new Sale($this->saleRepository->nextIdentity(), $coins);

        $this->saleRepository->save($sale);

        $coins->each(fn (Coin $coin) => $this->changeStockControl->addCoins($coin, 1));

        return new AddCreditResult($sale->getId()->getValue(), $sale->getCredit()->getAmount());
    }
}
