<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

use VendingMachine\Operation\Domain\Model\Coin;
use VendingMachine\Operation\Domain\Model\SaleId;
use VendingMachine\Operation\Domain\Model\SaleRepository;

final readonly class AddCreditService
{
    public function __construct(private SaleRepository $saleRepository)
    {
    }

    public function add(AddCreditCommand $command): AddCreditResult
    {
        $saleId = SaleId::ofNullable($command->getSaleId());
        $coin = new Coin($command->getCoinValue());

        $sale = $this->saleRepository->findOrCreateNewSale($saleId);

        $sale->addCredit($coin);

        $this->saleRepository->save($sale);

        return AddCreditResult::ok($sale->getCredit());
    }
}
