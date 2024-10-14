<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\Restock;

final class RestockCommand
{
    public function __construct(private string $productId, private int $quantity)
    {
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
