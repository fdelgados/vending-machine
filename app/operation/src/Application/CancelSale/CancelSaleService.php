<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\CancelSale;

use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Domain\Service\ChangeDispenser;

final readonly class CancelSaleService
{
    public function __construct(
        private SaleRepository $saleRepository,
        private ChangeDispenser $changeDispenser
    ) {
    }

    public function cancel(CancelCommand $command): Result
    {
        $saleId = SaleId::fromString($command->getSaleId());

        $sale = $this->saleRepository->saleOfId($saleId);

        if ($sale === null) {
            return Result::failure(Errors::saleNotFound());
        }

        $result = $this->changeDispenser->dispense($sale->getCredit());

        /** @var CoinCollection $change */
        $change = $result->getValue();

        $sale->cancel();

        $this->saleRepository->save($sale);

        return Result::success(
            $change->map(
                fn (Coin $coin) => $coin->getAmount()
            )
        );
    }
}
