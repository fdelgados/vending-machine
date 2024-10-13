<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\CancelSale;

use Faker\Factory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Operation\Domain\Model\Builders\CoinBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleIdMother;
use VendingMachine\Operation\Application\CancelSale\CancelCommand;
use VendingMachine\Operation\Application\CancelSale\CancelSaleService;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemorySaleRepository;

final class CancelSaleServiceTest extends TestCase
{
    private CancelSaleService $cancelSaleService;
    private SaleRepository $saleRepository;

    protected function setUp(): void
    {
        $this->saleRepository = new InMemorySaleRepository();
        $this->cancelSaleService = new CancelSaleService($this->saleRepository);

        parent::setUp();
    }

    #[Test]
    public function cancel_nonExistentSale_returnsNotFoundFailureResult(): void
    {
        $cancelCommand = $this->createCommand();

        $result = $this->cancelSaleService->cancel($cancelCommand);

        self::assertTrue($result->isFailure());
        self::assertEquals('sale_not_found', $result->getErrorCode());
    }

    #[Test]
    public function cancel_validSale_returnsSuccessResult(): void
    {
        $saleId = SaleIdMother::random();
        $cancelCommand = $this->createCommand($saleId->getValue());
        $sale = SaleBuilder::aSale()->ofId($saleId)->withCredit()->build();

        $this->saleRepository->save($sale);

        $result = $this->cancelSaleService->cancel($cancelCommand);

        self::assertTrue($result->isSuccess());
    }

    #[Test]
    public function cancel_validSale_returnsSuccessResultWithACoinToBeReturned(): void
    {
        $saleId = SaleIdMother::random();
        $cancelCommand = $this->createCommand($saleId->getValue());
        $sale = SaleBuilder::aSale()->ofId($saleId)->withCoin(CoinBuilder::aCoin()->ofValue(0.05)->build())->build();

        $this->saleRepository->save($sale);

        $result = $this->cancelSaleService->cancel($cancelCommand);

        self::assertSame([0.05], $result->getValue());
    }

    #[Test]
    public function cancel_validSale_returnsSuccessResultWithManyCoinsToBeReturned(): void
    {
        $saleId = SaleIdMother::random();
        $cancelCommand = $this->createCommand($saleId->getValue());
        $sale = SaleBuilder::aSale()
            ->ofId($saleId)
            ->withCoin(CoinBuilder::aCoin()->ofValue(0.05)->build())
            ->withCoin(CoinBuilder::aCoin()->ofValue(0.10)->build())
            ->withCoin(CoinBuilder::aCoin()->ofValue(1.0)->build())
            ->build();

        $this->saleRepository->save($sale);

        $result = $this->cancelSaleService->cancel($cancelCommand);

        self::assertSame([0.05, 0.10, 1.0], $result->getValue());
    }

    private function createCommand(?string $saleId = null): CancelCommand
    {
        return new CancelCommand($saleId ?? Factory::create()->uuid());
    }
}
