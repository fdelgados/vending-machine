<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\CancelSale;

use Faker\Factory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Common\Domain\CoinBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleIdMother;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Infrastructure\Outbound\InMemoryChangeStockControl;
use VendingMachine\Operation\Application\CancelSale\CancelCommand;
use VendingMachine\Operation\Application\CancelSale\CancelSaleService;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Domain\Service\ChangeDispenser;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemorySaleRepository;

final class CancelSaleServiceTest extends TestCase
{
    private CancelSaleService $cancelSaleService;
    private SaleRepository $saleRepository;

    protected function setUp(): void
    {
        $this->saleRepository = new InMemorySaleRepository();
        $changeDispenser = new ChangeDispenser(new InMemoryChangeStockControl());

        $this->cancelSaleService = new CancelSaleService($this->saleRepository, $changeDispenser);

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
        $this->createSale($saleId);

        $result = $this->cancelSaleService->cancel($this->createCommand($saleId->getValue()));

        self::assertTrue($result->isSuccess());
    }

    #[Test]
    public function cancel_validSale_returnsSuccessResultWithACoinToBeReturned(): void
    {
        $coin = CoinBuilder::aCoin()->ofValue(0.05)->build();
        $saleId = SaleIdMother::random();
        $this->createSale($saleId, $coin);

        $result = $this->cancelSaleService->cancel($this->createCommand($saleId->getValue()));

        self::assertSame([0.05], $result->getValue()->toArray());
    }

    #[Test]
    public function cancel_aValidSale_returnsSuccessResultWithCoinsToBeReturned(): void
    {
        $coins = [
            CoinBuilder::aCoin()->ofValue(0.05)->build(),
            CoinBuilder::aCoin()->ofValue(0.10)->build(),
            CoinBuilder::aCoin()->ofValue(1.0)->build(),
        ];
        $saleId = SaleIdMother::random();
        $this->createSale($saleId, ...$coins);

        $result = $this->cancelSaleService->cancel($this->createCommand($saleId->getValue()));

        self::assertSame([1.0, 0.10, 0.05], $result->getValue()->toArray());
    }

    private function createCommand(?string $saleId = null): CancelCommand
    {
        return new CancelCommand($saleId ?? Factory::create()->uuid());
    }

    private function createSale(SaleId $saleId, Coin ...$coins): void
    {
        $saleBuilder = SaleBuilder::aSale()->ofId($saleId);

        empty($coins) ? $saleBuilder->withCredit() : $saleBuilder->withCoins(...$coins);

        $this->saleRepository->save($saleBuilder->build());
    }
}
