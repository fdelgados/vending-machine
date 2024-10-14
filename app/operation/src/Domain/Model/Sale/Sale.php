<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Sale;

use DateTimeImmutable;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\Money;
use VendingMachine\Common\Domain\ProductId;

final class Sale
{
    private readonly SaleId $id;
    private readonly DateTimeImmutable $startedAt;
    private array $coins;
    private Credit $credit;
    private SaleState $state;
    private ?ProductId $productId;

    public function __construct(SaleId $saleId)
    {
        $this->id = $saleId;
        $this->startedAt = new DateTimeImmutable();
        $this->state = SaleState::IN_PROGRESS;
        $this->coins = [];
        $this->credit = new Credit(0.0);
        $this->productId = null;
    }

    public function getId(): SaleId
    {
        return $this->id;
    }

    public function addCredit(Coin $coin): void
    {
        precondition($this->state->isInProgress(), 'The sale is not in progress credits cannot be added.');

        $this->coins[] = $coin;
        $this->credit = $this->credit->plus($coin);
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

    public function selectProduct(ProductId $productId): void
    {
        precondition($this->state->isInProgress(), 'The sale is not in progress and products cannot be added.');

        $this->productId = $productId;
    }

    public function deductCredit(Money $money): void
    {
        $this->credit = $this->credit->minus($money);
    }

    public function getProductId(): ?ProductId
    {
        return $this->productId;
    }

    public function isCancelled(): bool
    {
        return $this->state->isCancelled();
    }
}
