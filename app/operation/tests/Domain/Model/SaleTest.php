<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model;

use Tests\VendingMachine\Operation\Domain\Model\Builders\CoinBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SaleTest extends TestCase
{
    #[Test]
    public function aNewlyCreatedSale_shouldHaveACreditEqualsToZero(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale()->build();

        self::assertEquals(0, $sale->getCredit());
    }

    #[Test]
    public function aNewlyCreatedSale_shouldHaveNoCoins(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale()->build();

        self::assertEquals(0, $sale->getNumberOfCoins());
    }

    #[Test]
    public function insertCoin_withValidCoin_shouldIncrementCredit(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale()->build();
        $creditBeforeInsertion = $sale->getCredit();

        $sale->addCredit(CoinBuilder::aCoin()->build());

        self::assertTrue($sale->getCredit() > $creditBeforeInsertion);
    }

    #[Test]
    public function insertCoin_withValidCoin_shouldIncrementTheNumberOfCoins(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale()->build();
        $coinsBeforeInsertion = $sale->getNumberOfCoins();

        $sale->addCredit(CoinBuilder::aCoin()->build());

        self::assertTrue($sale->getNumberOfCoins() > $coinsBeforeInsertion);
    }
}
