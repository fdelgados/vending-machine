<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Sale;

use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Common\Domain\CoinCollectionBuilder;
use Tests\VendingMachine\Common\Domain\ProductIdMother;
use Tests\VendingMachine\Operation\Domain\Model\Builders\PriceMother;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use VendingMachine\Operation\Domain\Model\Sale\Credit;

final class SaleTest extends TestCase
{
    #[Test]
    public function aNewlyCreatedSale_shouldHaveACreditGreaterThanZero(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale();

        self::assertTrue($sale->getCredit()->isGreaterThan(new Credit(0.0)));
    }

    #[Test]
    public function aNewlyCreatedSale_shouldHaveCoins(): void
    {
        $sale = SaleBuilder::aNewlyCreatedSale();

        self::assertGreaterThan(0, $sale->getAvailableCoins()->count());
    }

    #[Test]
    public function complete_toACancelledSale_throwsADomainException(): void
    {
        $cancelledSale = SaleBuilder::aSale()->cancelled()->build();
        $productId = ProductIdMother::random();

        assertThrows(
            DomainException::class,
            fn () => $cancelledSale->complete($productId, PriceMother::random())
        );
    }

    #[Test]
    public function cancel_shouldChangeTheSaleToCancelled(): void
    {
        $sale = SaleBuilder::aSale()->build();

        $sale->cancel();

        self::assertTrue($sale->isCancelled());
    }

    #[Test]
    public function cancel_shouldResetCreditToZero(): void
    {
        $sale = SaleBuilder::aSale()->build();

        $sale->cancel();

        self::assertEquals(0, $sale->getCredit()->getAmount());
    }

    #[Test]
    public function cancel_shouldEmptyTheAvailableCoins(): void
    {
        $sale = SaleBuilder::aSale()->build();

        $sale->cancel();

        self::assertCount(0, $sale->getAvailableCoins());
    }

    #[Test]
    public function addCredit_withValidCoin_shouldIncrementCredit(): void
    {
        $sale = SaleBuilder::aSale()->withCredit()->build();
        $creditBeforeInsertion = $sale->getCredit();

        $sale->addCredit(CoinCollectionBuilder::aCoinCollection()->withAnyCoin()->build());

        self::assertTrue($sale->getCredit()->isGreaterThan($creditBeforeInsertion));
    }

    #[Test]
    public function addCredit_withValidCoin_shouldIncrementTheNumberOfCoins(): void
    {
        $sale = SaleBuilder::aSale()->withCredit()->build();
        $coinsBeforeInsertion = count($sale->getAvailableCoins());

        $sale->addCredit(CoinCollectionBuilder::aCoinCollection()->withAnyCoin()->build());

        self::assertTrue(count($sale->getAvailableCoins()) > $coinsBeforeInsertion);
    }

    #[Test]
    public function addCredit_toACancelledSale_throwsADomainException(): void
    {
        $cancelledSale = SaleBuilder::aSale()->cancelled()->build();

        assertThrows(
            DomainException::class,
            fn () => $cancelledSale->addCredit(CoinCollectionBuilder::aCoinCollection()->build())
        );
    }
}
