<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Service;

use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Product\Product;
use VendingMachine\Operation\Domain\Model\Sale\Sale;

final class PurchaseProcessor
{
    public function purchase(Sale $sale, Product $product): Result
    {
        if ($product->isOutOfStock()) {
            return Result::failure(Errors::productOutOfStock());
        }

        if ($sale->getCredit()->isLessThan($product->getPrice())) {
            return Result::failure(Errors::insufficientCredit());
        }

        $sale->selectProduct($product->getId());
        $sale->deductCredit($product->getPrice());
        $product->decreaseStock();

        return Result::success();
    }
}
