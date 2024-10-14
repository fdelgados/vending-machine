<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Domain;

use VendingMachine\Common\Domain\ProductId;

interface ProductStock
{
    public function add(ProductId $productId, Quantity $quantity): void;

    public function get(ProductId $productId): int;
}
