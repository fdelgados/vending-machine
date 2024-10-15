<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\Purchase;

use VendingMachine\Common\Collection;

final readonly class PurchaseResult
{
    public function __construct(private string $productName, private Collection $change)
    {
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getChange(): Collection
    {
        return $this->change;
    }

    public function __toString(): string
    {
        $result = strtoupper($this->productName);
        if ($this->change->isEmpty()) {
            return $result;
        }

        return sprintf('%s, %s', $result, $this->change);
    }
}
