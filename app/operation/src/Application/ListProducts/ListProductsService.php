<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\ListProducts;

use VendingMachine\Operation\Domain\Model\Product\Product;
use VendingMachine\Operation\Domain\Model\Product\ProductRepository;

final class ListProductsService
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function list(): ProductMap
    {
        return $this->assemble($this->productRepository->findAll());
    }

    private function assemble(array $products): ProductMap
    {
        $productMap = new ProductMap();

        /** @var Product $product */
        foreach ($products as $product) {
            $productId = $product->getId()->value();
            $productMap->add(
                $productId,
                new ProductDto($productId, (string) $product->getName(), $product->getPrice()->getAmount())
            );
        }

        return $productMap;
    }
}
