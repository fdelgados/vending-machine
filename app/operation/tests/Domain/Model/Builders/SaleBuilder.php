<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use ReflectionClass;
use VendingMachine\Operation\Domain\Model\Coin;
use VendingMachine\Operation\Domain\Model\Sale;
use VendingMachine\Operation\Domain\Model\SaleId;

final class SaleBuilder
{
    /** @var array<Coin> */
    private array $coins = [];
    private SaleId $id;

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

    public function build(): Sale
    {
        $sale = new Sale($this->id);

        $this->addCredits($sale);

        return $sale;
    }

    private function addCredits(Sale $sale): void
    {
        foreach ($this->coins as $coin) {
            $sale->addCredit($coin);
        }
    }
}
