<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\Purchase;

final readonly class PurchaseCommand
{
    public function __construct(private string $saleId, private string $productId)
    {
    }

    public function getSaleId(): string
    {
        return $this->saleId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}
