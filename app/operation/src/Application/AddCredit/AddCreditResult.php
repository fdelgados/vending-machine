<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

final readonly class AddCreditResult
{
    public function __construct(private string $saleId, private float $credit)
    {
    }

    public function getSaleId(): string
    {
        return $this->saleId;
    }

    public function getCredit(): float
    {
        return $this->credit;
    }
}
