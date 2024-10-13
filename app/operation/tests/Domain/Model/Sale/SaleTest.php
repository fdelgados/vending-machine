<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Sale;

use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Operation\Domain\Model\Builders\CoinBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\PriceMother;
use Tests\VendingMachine\Operation\Domain\Model\Builders\ProductBuilder;
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
        $product = ProductBuilder::aProduct()->build();

        assertThrows(
            DomainException::class,
            fn () => $cancelledSale->selectProduct($product)
        );
    }

    #[Test]
    public function selectProduct_outOfStock_returnsAFailureResult(): void
    {
        $coin = CoinBuilder::aCoin()->ofValue(1.0)->build();
        $sale = SaleBuilder::aSale()
            ->withCoin($coin)
            ->build();
        $product = ProductBuilder::aProduct()
            ->pricedAt(PriceMother::ofAmount(1.0))
            ->outOfStock()
            ->build();

        $result = $sale->selectProduct($product);

        self::assertTrue($result->isFailure());
        self::assertEquals('product_out_of_stock', $result->getErrorCode());
    }

    #[Test]
    public function selectProduct_withInsufficientCredit_returnsAFailureResult(): void
    {
        $sale = SaleBuilder::aSale()->withCoin(CoinBuilder::aCoin()->ofValue(0.05)->build())->build();
        $product = ProductBuilder::aProduct()->pricedAt(PriceMother::ofAmount(1.0))->build();

        $result = $sale->selectProduct($product);

        self::assertTrue($result->isFailure());
        self::assertEquals('insufficient_credit', $result->getErrorCode());
    }

    #[Test]
    public function selectProduct_withSufficientCredit_reduceCredit(): void
    {
        $credit = 1.0;
        $price = 0.65;

        $sale = SaleBuilder::aSale()->withCreditOf($credit)->build();
        $product = ProductBuilder::aProduct()->pricedAt(PriceMother::ofAmount($price))->build();

        $sale->selectProduct($product);

        self::assertSame(0.35, $sale->getCredit()->getAmount());
    }

    #[Test]
    public function selectProduct_withSufficientCredit_addAProductToSale(): void
    {
        $credit = 1.0;
        $sale = SaleBuilder::aSale()->withCreditOf($credit)->build();
        $product = ProductBuilder::aProduct()->pricedAt(PriceMother::ofAmount($credit))->build();

        $sale->selectProduct($product);

        self::assertNotNull($sale->getProduct());
    }

    #[Test]
    public function selectProduct_withSufficientCredit_reducesTheProductAvailableStockByOne(): void
    {
        $credit = 1.0;
        $sale = SaleBuilder::aSale()->withCreditOf($credit)->build();
        $product = ProductBuilder::aProduct()
            ->pricedAt(PriceMother::ofAmount($credit))
            ->withAvailableStock()
            ->build();

        $stockBeforeSelection = $product->getAvailableStock();

        $sale->selectProduct($product);

        self::assertEquals(1, $stockBeforeSelection - $sale->getProduct()->getAvailableStock());
    }
}
