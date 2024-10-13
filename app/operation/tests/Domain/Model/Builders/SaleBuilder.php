<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use VendingMachine\Operation\Domain\Model\Product\ProductId;
use VendingMachine\Operation\Domain\Model\Sale\Coin;
use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;

final class SaleBuilder
{
    /** @var array<Coin> */
    private array $coins = [];
    private SaleId $id;
    private bool $isCancelled = false;
    private ?ProductId $productId = null;

    private function __construct()
    {
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
            $this->coins[] = CoinBuilder::aCoin()->build();
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
        $this->coins[] = $coin;

        return $this;
    }

    public function withProductId(ProductId $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function withCreditOf(float $credit): self
    {
        $this->coins[] = CoinBuilder::aCoin()->ofValue($credit)->build();

        return $this;
    }

    public function build(): Sale
    {
        $sale = new Sale($this->id);

        $this->addCredits($sale);
        if ($this->productId) {
            $sale->selectProduct($this->productId);
        }

        if ($this->isCancelled) {
            $sale->cancel();
        }

        return $sale;
    }

    private function addCredits(Sale $sale): void
    {
        foreach ($this->coins as $coin) {
            $sale->addCredit($coin);
        }
    }
}
