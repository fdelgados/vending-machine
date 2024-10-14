<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Service;

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Product\Product;
use VendingMachine\Operation\Domain\Model\Sale\Sale;

class PurchaseProcessor
{
    public function __construct(
        private readonly ChangeDispenser $changeDispenser,
        private readonly ChangeStockControl $changeStockControl
    ) {
    }

    public function purchase(Sale $sale, Product $product): Result
    {
        if ($product->isOutOfStock()) {
            return Result::failure(Errors::productOutOfStock());
        }

        if ($sale->getCredit()->isLessThan($product->getPrice())) {
            return Result::failure(Errors::insufficientCredit());
        }

        $credit = $sale->getCredit()->minus($product->getPrice());
        if (!$this->changeStockControl->hasEnoughChange($credit->getAmount())) {
            return Result::failure(Errors::notEnoughChange());
        }

        $sale->selectProduct($product->getId());
        $sale->deductCredit($product->getPrice());
        $product->decreaseStock();

        return Result::success($this->changeDispenser->dispense($sale->getCredit()));
    }
}
