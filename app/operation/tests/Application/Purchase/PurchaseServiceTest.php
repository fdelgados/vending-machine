<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\Purchase;

use Faker\Factory;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Common\Domain\CoinCollectionBuilder;
use Tests\VendingMachine\Common\Domain\ProductIdMother;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleIdMother;
use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Common\Result;
use VendingMachine\Operation\Application\Purchase\PurchaseCommand;
use VendingMachine\Operation\Application\Purchase\PurchaseService;
use VendingMachine\Operation\Domain\Errors;
use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Service\PurchaseProcessor;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemoryProductRepository;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemorySaleRepository;

final class PurchaseServiceTest extends TestCase
{
    private InMemorySaleRepository $saleRepository;
    private PurchaseService $purchaseService;
    private PurchaseProcessor|MockInterface $purchaseProcessor;

    protected function setUp(): void
    {
        $this->saleRepository = new InMemorySaleRepository();
        $this->purchaseProcessor = Mockery::mock(PurchaseProcessor::class);

        $this->purchaseService = new PurchaseService(
            $this->saleRepository,
            new InMemoryProductRepository(),
            $this->purchaseProcessor
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
        $command = $this->createCommand($sale->getId(), ProductIdMother::oneOf('1', '2', '3'));

        $this->purchaseProcessor->shouldReceive('purchase')->andReturn($failure);

        $result = $this->purchaseService->purchase($command);

        self::assertTrue($result->isFailure());
    }

    #[Test]
    #[DataProvider('purchaseProcessorFailures')]
    public function purchase_whenPurchaseProcessorReturnsFailure_saleIsCancelled(Result $failure): void
    {
        $sale = $this->createASale();
        $command = $this->createCommand($sale->getId(), ProductIdMother::oneOf('1', '2', '3'));

        $this->purchaseProcessor->shouldReceive('purchase')->andReturn($failure);

        $this->purchaseService->purchase($command);

        self::assertTrue($sale->isCancelled());
    }

    #[Test]
    public function purchase_whenPurchaseProcessorReturnsSuccess_returnSuccessResult(): void
    {
        $sale = $this->createASale();
        $command = $this->createCommand($sale->getId(), ProductIdMother::oneOf('1', '2', '3'));

        $this->purchaseProcessor
            ->shouldReceive('purchase')
            ->andReturn(Result::success(CoinCollectionBuilder::aCoinCollection()->build()));

        $result = $this->purchaseService->purchase($command);

        self::assertTrue($result->isSuccess());
    }

    public static function purchaseProcessorFailures(): array
    {
        return [
            [Result::failure(Errors::productOutOfStock())],
            [Result::failure(Errors::notEnoughChange())],
            [Result::failure(Errors::insufficientCredit())],
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

        $sale = SaleBuilder::aSale()
            ->ofId($saleId)
            ->withCredit()
            ->build();

        $this->saleRepository->save($sale);

        return $sale;
    }
}
