<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Sale;

use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Common\Domain\ProductIdMother;
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
