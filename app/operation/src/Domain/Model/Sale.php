<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model;

use DateTimeImmutable;

final class Sale
{
    private readonly SaleId $id;
    private readonly DateTimeImmutable $startedAt;
    private array $coins;
    private float $credit;
    private SaleState $state;

    public function __construct(SaleId $saleId)
    {
        $this->id = $saleId;
        $this->startedAt = new DateTimeImmutable();
        $this->state = SaleState::IN_PROGRESS;
        $this->coins = [];
        $this->credit = 0.0;
    }

    public function getId(): SaleId
    {
        return $this->id;
    }

    public function addCredit(Coin $coin): void
    {
        precondition($this->state->isInProgress(), 'The sale is not in progress credits cannot be added.');

        $this->coins[] = $coin;
        $this->credit += $coin->getValue();
    }

    public function getCredit(): float
    {
        return $this->credit;
    }

    /**
     * @return Coin[]
     */
    public function getAvailableCoins(): array
    {
        return $this->coins;
    }

    public function cancel(): void
    {
        precondition($this->state->isInProgress(), 'The sale is not in progress and cannot be cancelled.');

        $this->state = SaleState::CANCELLED;
    }
}
