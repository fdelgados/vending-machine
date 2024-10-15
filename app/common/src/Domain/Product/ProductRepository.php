<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain\Product;

use VendingMachine\Common\Domain\ProductId;

interface ProductRepository
{
    public function productOfId(ProductId $productId): ?Product;

    public function save(Product $product): void;

    public function findAll(): array;
}
