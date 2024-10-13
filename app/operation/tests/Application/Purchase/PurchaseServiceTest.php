<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\Purchase;

use Faker\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Operation\Domain\Model\Builders\ProductIdMother;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleIdMother;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Application\Purchase\PurchaseCommand;
use VendingMachine\Operation\Application\Purchase\PurchaseService;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Product\ProductId;
use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Service\ChangeCalculator;
use VendingMachine\Operation\Domain\Service\PurchaseProcessor;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemoryProductRepository;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemorySaleRepository;

final class PurchaseServiceTest extends TestCase
{
    private InMemorySaleRepository $saleRepository;
    private PurchaseService $purchaseService;
    private PurchaseProcessor|MockObject $purchaseProcessor;
    private ChangeCalculator|MockObject $changeCalculator;

    protected function setUp(): void
    {
        $this->saleRepository = new InMemorySaleRepository();
        $this->purchaseProcessor = $this->createMock(PurchaseProcessor::class);
        $this->changeCalculator = $this->createMock(ChangeCalculator::class);

        $this->purchaseService = new PurchaseService(
            $this->saleRepository,
            new InMemoryProductRepository(),
            $this->purchaseProcessor,
            $this->changeCalculator
        );

        parent::setUp();
    }

    #[Test]
    public function purchase_aNonExistentSale_returnSaleNotFoundFailureResult(): void
    {
        $command = $this->createCommand();

        $result = $this->purchaseService->purchase($command);

        self::assertTrue($result->isFailure());
        self::assertEquals('sale_not_found', $result->getErrorCode());
    }

    #[Test]
    public function purchase_aNonExistentSale_returnProductNotFoundFailureResult(): void
    {
        $sale = $this->createASale();
        $command = $this->createCommand($sale->getId(), ProductIdMother::ofId('100'));

        $result = $this->purchaseService->purchase($command);

        self::assertTrue($result->isFailure());
        self::assertEquals('product_not_found', $result->getErrorCode());
    }

    #[Test]
    #[DataProvider('purchaseProcessorFailures')]
    public function purchase_whenPurchaseProcessorReturnsFailure_returnFailureResult(Result $failure): void
    {
        $sale = $this->createASale();
        $command = $this->createCommand($sale->getId(), $sale->getProductId());

        $this->changeCalculator
            ->method('calculate')
            ->willReturn(Result::success());
        $this->purchaseProcessor
            ->method('purchase')
            ->willReturn($failure);

        $result = $this->purchaseService->purchase($command);

        self::assertTrue($result->isFailure());
    }

    #[Test]
    public function purchase_whenPurchaseProcessorReturnsSuccess_returnSuccessResult(): void
    {
        $sale = $this->createASale();
        $command = $this->createCommand($sale->getId(), $sale->getProductId());

        $this->changeCalculator
            ->method('calculate')
            ->willReturn(Result::success());
        $this->purchaseProcessor
            ->method('purchase')
            ->willReturn(Result::success());

        $result = $this->purchaseService->purchase($command);

        self::assertTrue($result->isSuccess());
    }

    #[Test]
    public function purchase_whenChangeCalculatorReturnsFailure_returnsFailureResult(): void
    {
        $sale = $this->createASale();
        $command = $this->createCommand($sale->getId(), $sale->getProductId());

        $this->changeCalculator
            ->method('calculate')
            ->willReturn(Result::failure(Errors::notEnoughChange()));

        $result = $this->purchaseService->purchase($command);

        self::assertTrue($result->isFailure());
        self::assertEquals('not_enough_change', $result->getErrorCode());
    }

    public static function purchaseProcessorFailures(): array
    {
        return [
            [Result::failure(Errors::productOutOfStock())],
            [Result::failure(Errors::productNotFound())],
        ];
    }

    private function createCommand(?SaleId $saleId = null, ?ProductId $productId = null): PurchaseCommand
    {
        $faker = Factory::create();

        return new PurchaseCommand(
            $saleId ? $saleId->getValue() : $faker->uuid(),
            $productId ? $productId->value() : (string) $faker->randomDigitNot(0)
        );
    }

    private function createASale(): Sale
    {
        $saleId = SaleIdMother::random();
        $productId = ProductIdMother::ofId('1');

        $sale = SaleBuilder::aSale()
            ->ofId($saleId)
            ->withCredit()
            ->withProductId($productId)
            ->build();

        $this->saleRepository->save($sale);

        return $sale;
    }
}
