<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\Restock;

use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Maintenance\Domain\ProductStock;
use VendingMachine\Maintenance\Domain\Quantity;

final readonly class RestockService
{
    public function __construct(private ProductStock $productStock)
    {
    }

    public function restock(RestockCommand $command): void
    {
        $productId = new ProductId($command->getProductId());
        $quantity = new Quantity($command->getQuantity());

        $this->productStock->add($productId, $quantity);
    }
}
