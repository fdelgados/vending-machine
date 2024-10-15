<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\Restock;

use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Maintenance\Domain\Quantity;

final readonly class RestockService
{
    public function restock(RestockCommand $command): void
    {
        $productId = new ProductId($command->getProductId());
        $quantity = new Quantity($command->getQuantity());
    }
}
