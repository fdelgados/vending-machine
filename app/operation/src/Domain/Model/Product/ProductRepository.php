<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Product;

use VendingMachine\Common\Domain\ProductId;

interface ProductRepository
{
    public function productOfId(ProductId $productId): ?Product;

    public function save(Product $product): void;
}
