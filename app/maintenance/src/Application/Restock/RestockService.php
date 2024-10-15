<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\Restock;

use VendingMachine\Common\Domain\Product\ProductRepository;
use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Common\Domain\Quantity;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;

final readonly class RestockService
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function restock(RestockCommand $command): Result
    {
        $productId = new ProductId($command->getProductId());
        $quantity = new Quantity($command->getQuantity());

        $product = $this->productRepository->productOfId($productId);

        if ($product === null) {
            return Result::failure(Errors::productNotFound());
        }

        $product->increaseStock($quantity);

        $this->productRepository->save($product);

        return Result::success();
    }
}
