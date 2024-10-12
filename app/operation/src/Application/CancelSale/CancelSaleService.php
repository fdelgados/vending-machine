<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\CancelSale;

use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Coin;
use VendingMachine\Operation\Domain\Model\SaleId;
use VendingMachine\Operation\Domain\Model\SaleRepository;

final readonly class CancelSaleService
{
    public function __construct(private SaleRepository $saleRepository)
    {
    }

    public function cancel(CancelCommand $command): Result
    {
        $saleId = SaleId::fromString($command->getSaleId());

        $sale = $this->saleRepository->saleOfId($saleId);

        if ($sale === null) {
            return Result::failure(Errors::saleNotFound());
        }

        $sale->cancel();

        $this->saleRepository->save($sale);

        return Result::success(
            array_map(
                fn (Coin $coin) => $coin->getValue(),
                $sale->getAvailableCoins()
            )
        );
    }
}
