<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

final readonly class AddCreditCommand
{
    public function __construct(private float $coinValue, private ?string $saleId)
    {
    }

    public function getCoinValue(): float
    {
        return $this->coinValue;
    }

    public function getSaleId(): ?string
    {
        return $this->saleId;
    }
}
