<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use Tests\VendingMachine\Common\Domain\CoinBuilder;
use Tests\VendingMachine\Common\Domain\CoinCollectionBuilder;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;

final class SaleBuilder
{
    private CoinCollection $coins;
    private SaleId $id;
    private bool $isCancelled = false;
    private ?ProductId $productId = null;

    private function __construct()
    {
        $this->coins = new CoinCollection();
        $this->id = SaleIdMother::random();
    }

    public static function aNewlyCreatedSale(): Sale
    {
        return self::aSale()->build();
    }

    public static function aSale(): self
    {
        return new self();
    }

    public function ofId(SaleId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withCredit(): self
    {
        $numberOfCoins = rand(1, 5);

        for ($i = 0; $i < $numberOfCoins; $i++) {
            $this->coins->add(CoinBuilder::aCoin()->build());
        }

        return $this;
    }

    public function cancelled(): self
    {
        $this->isCancelled = true;

        return $this;
    }

    public function withCoin(Coin $coin): self
    {
        $this->coins->add($coin);

        return $this;
    }

    public function withCoins(Coin ...$coins): self
    {
        $this->coins = new CoinCollection(...$coins);

        return $this;
    }

    public function withProductId(ProductId $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function withCreditOf(float $credit): self
    {
        $this->coins->add(CoinBuilder::aCoin()->ofValue($credit)->build());

        return $this;
    }

    public function build(): Sale
    {
        $coins = $this->coins->isNotEmpty()
            ? $this->coins
            : CoinCollectionBuilder::aCoinCollection()->withAnyCoin()->build();

        $sale = new Sale($this->id, $coins);

        if ($this->productId) {
            $sale->selectProduct($this->productId);
        }

        if ($this->isCancelled) {
            $sale->cancel();
        }

        return $sale;
    }
}
