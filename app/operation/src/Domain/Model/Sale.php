<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model;

use DateTimeImmutable;

final class Sale
{
    private readonly SaleId $id;
    private readonly DateTimeImmutable $startedAt;
    private array $coins;
    private float $credit;

    public function __construct()
    {
        $this->id = SaleId::generate();
        $this->startedAt = new DateTimeImmutable();
        $this->coins = [];
        $this->credit = 0.0;
    }

    public function getId(): SaleId
    {
        return $this->id;
    }

    public function addCredit(Coin $coin): void
    {
        $this->coins[] = $coin;
        $this->credit += $coin->value();
    }

    public function getCredit(): float
    {
        return $this->credit;
    }

    public function getNumberOfCoins(): int
    {
        return count($this->coins);
    }

    public function cancel(): void
    {
        $this->coins = [];
        $this->credit = 0.0;
    }
}
