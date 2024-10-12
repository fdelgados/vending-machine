<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model;

use DateTimeImmutable;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Product\Product;

final class Sale
{
    private readonly SaleId $id;
    private readonly DateTimeImmutable $startedAt;
    private array $coins;
    private Credit $credit;
    private SaleState $state;
    private ?Product $product;

    public function __construct(SaleId $saleId)
    {
        $this->id = $saleId;
        $this->startedAt = new DateTimeImmutable();
        $this->state = SaleState::IN_PROGRESS;
        $this->coins = [];
        $this->credit = new Credit(0.0);
        $this->product = null;
    }

    public function getId(): SaleId
    {
        return $this->id;
    }

    public function addCredit(Coin $coin): void
    {
        precondition($this->state->isInProgress(), 'The sale is not in progress credits cannot be added.');

        $this->coins[] = $coin;
        $this->credit = $this->credit->sum($coin->getValue());
    }

    public function getCredit(): Credit
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
