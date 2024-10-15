<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Sale;

use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Common\Domain\Money;
use VendingMachine\Common\Domain\ProductId;

final class Sale
{
    private readonly SaleId $id;
    private CoinCollection $coins;
    private Credit $credit;
    private SaleState $state;
    private ?ProductId $productId;

    public function __construct(SaleId $saleId, CoinCollection $coins)
    {
        ensure($coins->isNotEmpty(), 'You must insert at least one coin to start a sale.');

        $this->id = $saleId;
        $this->state = SaleState::IN_PROGRESS;
        $this->productId = null;
        $this->credit = Credit::zero();
        $this->coins = new CoinCollection();

        $this->addCredit($coins);
    }

    public function addCredit(CoinCollection $coins): void
    {
        precondition($this->state->isInProgress(), 'The sale is not in progress and cannot be cancelled.');
        precondition($coins->isNotEmpty(), 'You must insert at least one coin to add credit.');

        $coins->each(
            fn (Coin $coin) => $this->credit = $this->credit->plus($coin)
        );
        $this->coins = $this->coins->merge($coins);
    }

    public function getId(): SaleId
    {
        return $this->id;
    }

    public function getCredit(): Credit
    {
        return $this->credit;
    }

    public function getAvailableCoins(): CoinCollection
    {
        return $this->coins;
    }

    public function cancel(): void
    {
        precondition($this->isCancellable(), 'The sale cannot be cancelled.');

        if ($this->state->isCancelled()) {
            return;
        }

        $this->credit = Credit::zero();
        $this->coins = new CoinCollection();
        $this->state = SaleState::CANCELLED;
    }

    private function isCancellable(): bool
    {
        return $this->state->isInProgress() || $this->state->isCancelled();
    }

    public function complete(ProductId $productId, Money $price): void
    {
        precondition($this->state->isInProgress(), 'The sale is not in progress and products cannot be added.');

        $this->productId = $productId;
        $this->credit = $this->credit->minus($price);
        $this->state = SaleState::COMPLETED;
    }

    public function getProductId(): ?ProductId
    {
        return $this->productId;
    }

    public function isCancelled(): bool
    {
        return $this->state->isCancelled();
    }

    public function getState(): SaleState
    {
        return $this->state;
    }
}
