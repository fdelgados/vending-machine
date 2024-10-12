<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\CancelSale;

final readonly class CancelCommand
{
    public function __construct(private string $saleId)
    {
    }

    public function getSaleId(): string
    {
        return $this->saleId;
    }
}
