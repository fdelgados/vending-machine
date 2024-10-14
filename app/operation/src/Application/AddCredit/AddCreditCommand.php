<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

final readonly class AddCreditCommand
{
    private array $coinValues;

    public function __construct(float ...$coinValues)
    {
        $this->coinValues = $coinValues;
    }

    public function getCoinValues(): array
    {
        return $this->coinValues;
    }
}
