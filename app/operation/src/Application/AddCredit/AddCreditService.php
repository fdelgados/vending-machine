<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

use VendingMachine\Common\Domain\ChangeDispenser;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;

final readonly class AddCreditService
{
    public function __construct(
        private SaleRepository $saleRepository,
        private ChangeDispenser $changeDispenser
    ) {
    }

    public function add(AddCreditCommand $command): Result
    {
        $saleId = SaleId::ofNullable($command->getSaleId());
        $coin = new Coin($command->getCoinValue());

        $sale = $this->saleRepository->findOrCreateNewSale($saleId);

        $sale->addCredit($coin);

        $this->saleRepository->save($sale);
        $this->changeDispenser->addCoins($coin, 1);

        return Result::success($sale->getCredit());
    }
}
