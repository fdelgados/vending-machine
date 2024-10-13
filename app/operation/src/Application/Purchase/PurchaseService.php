<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\Purchase;

use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Product\ProductId;
use VendingMachine\Operation\Domain\Model\Product\ProductRepository;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Domain\Service\PurchaseProcessor;

final readonly class PurchaseService
{
    public function __construct(
        private SaleRepository $saleRepository,
        private ProductRepository $productRepository,
        private PurchaseProcessor $purchaseProcessor
    ) {
    }

    public function purchase(PurchaseCommand $command): Result
    {
        $saleId = SaleId::fromString($command->getSaleId());
        $productId = new ProductId($command->getProductId());

        $sale = $this->saleRepository->saleOfId($saleId);

        if ($sale === null) {
            return Result::failure(Errors::saleNotFound());
        }

        $product = $this->productRepository->productOfId($productId);

        if ($product === null) {
            return Result::failure(Errors::productNotFound());
        }

        $result = $this->purchaseProcessor->purchase($sale, $product);

        if ($result->isFailure()) {
            return $result;
        }

        return Result::success();
    }
}
