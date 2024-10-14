<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Sale;

use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Common\Domain\CoinBuilder;
use Tests\VendingMachine\Common\Domain\ProductIdMother;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use VendingMachine\Operation\Domain\Model\Sale\Credit;

final class SaleTest extends TestCase
{
    #[Test]
    public function aNewlyCreatedSale_shouldHaveACreditEqualsToZero(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale();

        self::assertTrue($sale->getCredit()->equals(new Credit(0.0)));
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
        $sale = SaleBuilder::aSale()->withCredit()->build();
        $creditBeforeInsertion = $sale->getCredit();

        $sale->addCredit(CoinBuilder::aCoin()->build());

        self::assertTrue($sale->getCredit()->isGreaterThan($creditBeforeInsertion));
    }

    #[Test]
    public function addCredit_withValidCoin_shouldIncrementTheNumberOfCoins(): void
    {
        $sale = SaleBuilder::aSale()->withCredit()->build();
        $coinsBeforeInsertion = count($sale->getAvailableCoins());

        $sale->addCredit(CoinBuilder::aCoin()->build());

        self::assertTrue(count($sale->getAvailableCoins()) > $coinsBeforeInsertion);
    }

    #[Test]
    public function addCredit_toACancelledSale_throwsADomainException(): void
    {
        $cancelledSale = SaleBuilder::aSale()->cancelled()->build();

        assertThrows(
            DomainException::class,
            fn () => $cancelledSale->addCredit(CoinBuilder::aCoin()->build())
        );
    }

    #[Test]
    public function selectProduct_toACancelledSale_throwsADomainException(): void
    {
        $cancelledSale = SaleBuilder::aSale()->cancelled()->build();
        $productId = ProductIdMother::random();

        assertThrows(
            DomainException::class,
            fn () => $cancelledSale->selectProduct($productId)
        );
    }

    #[Test]
    public function cancel_shouldChangeTheSaleToCancelled(): void
    {
        $sale = SaleBuilder::aSale()->build();

        $sale->cancel();

        self::assertTrue($sale->isCancelled());
    }
}
