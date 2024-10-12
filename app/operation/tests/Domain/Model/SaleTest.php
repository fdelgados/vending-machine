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
        $sale = SaleBuilder::aNewlyCreatedSale();

        self::assertEquals(0, $sale->getCredit());
    }

    #[Test]
    public function aNewlyCreatedSale_shouldHaveNoCoins(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale();

        self::assertCount(0, $sale->getAvailableCoins());
    }

    #[Test]
    public function addCredit_withValidCoin_shouldIncrementCredit(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale();
        $creditBeforeInsertion = $sale->getCredit();

        $sale->addCredit(CoinBuilder::aCoin()->build());

        self::assertTrue($sale->getCredit() > $creditBeforeInsertion);
    }

    #[Test]
    public function addCredit_withValidCoin_shouldIncrementTheNumberOfCoins(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale();
        $coinsBeforeInsertion = count($sale->getAvailableCoins());

        $sale->addCredit(CoinBuilder::aCoin()->build());

        self::assertTrue(count($sale->getAvailableCoins()) > $coinsBeforeInsertion);
    }

    #[Test]
    public function addCredit_toACancelledSale_throwsADomainException(): void
    {
        $cancelledSale = SaleBuilder::aSale()->cancelled()->build();

        assertThrows(
            \DomainException::class,
            fn () => $cancelledSale->addCredit(CoinBuilder::aCoin()->build())
        );
    }
}
