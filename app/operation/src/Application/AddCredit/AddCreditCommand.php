<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

final readonly class AddCreditCommand
{
    private array $coinValues;
    private ?string $saleId;

    public function __construct(?string $saleId, float ...$coinValues)
    {
        $this->saleId = $saleId;
        $this->coinValues = $coinValues;
    }

    public function getCoinValues(): array
    {
        return $this->coinValues;
    }

    public function getSaleId(): ?string
    {
        return $this->saleId;
    }
}
