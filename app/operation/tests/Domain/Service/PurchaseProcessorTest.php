<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Service;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Common\Domain\CoinBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\PriceMother;
use Tests\VendingMachine\Operation\Domain\Model\Builders\ProductBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Operation\Domain\Service\ChangeDispenser;
use VendingMachine\Operation\Domain\Service\PurchaseProcessor;

final class PurchaseProcessorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private PurchaseProcessor $purchaseProcessor;
    private ChangeStockControl|MockInterface $changeStockControl;

    protected function setUp(): void
    {
        $changeDispenser = $this->createChangeDispenser();
        $this->changeStockControl = $this->createChangeStockControl();

        $this->purchaseProcessor = new PurchaseProcessor(
            $changeDispenser,
            $this->changeStockControl
        );

        parent::setUp();
    }

    #[Test]
    public function purchase_outOfStock_returnsAFailureResult(): void
    {
        $coin = CoinBuilder::aCoin()->ofValue(1.0)->build();
        $sale = SaleBuilder::aSale()
            ->withCoin($coin)
            ->build();
        $product = ProductBuilder::aProduct()
            ->pricedAt(PriceMother::ofAmount(1.0))
            ->outOfStock()
            ->build();

        $result = $this->purchaseProcessor->purchase($sale, $product);

        self::assertTrue($result->isFailure());
        self::assertEquals('product_out_of_stock', $result->getErrorCode());
    }

    #[Test]
    public function purchase_withInsufficientCredit_returnsAFailureResult(): void
    {
        $sale = SaleBuilder::aSale()->withCoin(CoinBuilder::aCoin()->ofValue(0.05)->build())->build();
        $product = ProductBuilder::aProduct()->pricedAt(PriceMother::ofAmount(1.0))->build();

        $result = $this->purchaseProcessor->purchase($sale, $product);

        self::assertTrue($result->isFailure());
        self::assertEquals('insufficient_credit', $result->getErrorCode());
    }

    #[Test]
    public function purchase_withNotEnoughChange_returnsAFailureResult(): void
    {
        $sale = SaleBuilder::aSale()->withCreditOf(1.0)->build();
        $product = ProductBuilder::aProduct()->pricedAt(PriceMother::ofAmount(0.65))->build();

        $this->changeStockControl->shouldReceive('hasEnoughChange')->andReturn(false);

        $result = $this->purchaseProcessor->purchase($sale, $product);

        self::assertTrue($result->isFailure());
        self::assertEquals('not_enough_change', $result->getErrorCode());
    }

    #[Test]
    public function purchase_withSufficientCredit_reduceCredit(): void
    {
        $credit = 1.0;
        $price = 0.65;

        $sale = SaleBuilder::aSale()->withCreditOf($credit)->build();
        $product = ProductBuilder::aProduct()->pricedAt(PriceMother::ofAmount($price))->build();

        $this->purchaseProcessor->purchase($sale, $product);

        self::assertSame(0.35, $sale->getCredit()->getAmount());
    }

    #[Test]
    public function purchase_withSufficientCredit_addAProductToSale(): void
    {
        $credit = 1.0;
        $sale = SaleBuilder::aSale()->withCreditOf($credit)->build();
        $product = ProductBuilder::aProduct()->pricedAt(PriceMother::ofAmount($credit))->build();

        $this->purchaseProcessor->purchase($sale, $product);

        self::assertNotNull($sale->getProductId());
    }

    #[Test]
    public function purchase_withSufficientCredit_reducesTheProductAvailableStockByOne(): void
    {
        $credit = 1.0;
        $sale = SaleBuilder::aSale()->withCreditOf($credit)->build();
        $product = ProductBuilder::aProduct()
            ->pricedAt(PriceMother::ofAmount($credit))
            ->withAvailableStock()
            ->build();

        $stockBeforeSelection = $product->getAvailableStock();

        $this->purchaseProcessor->purchase($sale, $product);

        self::assertEquals(1, $stockBeforeSelection - $product->getAvailableStock());
    }

    private function createChangeDispenser(): ChangeDispenser|MockInterface
    {
        $changeDispenser = \Mockery::mock(ChangeDispenser::class);
        $changeDispenser->shouldReceive('dispense')->andReturn(new CoinCollection());

        return $changeDispenser;
    }

    private function createChangeStockControl(): ChangeStockControl|MockInterface
    {
        $changeStockControl = \Mockery::mock(ChangeStockControl::class);
        $changeStockControl->shouldReceive('hasEnoughChange')->andReturn(true)->byDefault();

        return $changeStockControl;
    }
}
