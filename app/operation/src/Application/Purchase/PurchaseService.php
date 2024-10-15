<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\Purchase;

use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Common\Error;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Product\Product;
use VendingMachine\Operation\Domain\Model\Product\ProductRepository;
use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Domain\Service\PurchaseProcessor;

final readonly class PurchaseService
{
    public function __construct(
        private SaleRepository $saleRepository,
        private ProductRepository $productRepository,
        private PurchaseProcessor $purchaseProcessor,
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

        return $result->match(
            success: fn (CoinCollection $change) => $this->handleSuccess($sale, $product, $change),
            failure: fn (Error $error) => $result
        );
    }

    private function handleSuccess(Sale $sale, Product $product, CoinCollection $change): Result
    {
        $this->saleRepository->save($sale);
        $this->productRepository->save($product);

        $purchaseResult = new PurchaseResult(
            (string) $product->getName(),
            $change->map(fn (Coin $coin) => $coin->getAmount())
        );

        return Result::success($purchaseResult);
    }
}
