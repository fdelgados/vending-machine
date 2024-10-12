<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\AddCredit;

use Faker\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleBuilder;
use VendingMachine\Operation\Application\AddCredit\AddCreditCommand;
use VendingMachine\Operation\Application\AddCredit\AddCreditService;
use VendingMachine\Operation\Domain\Model\SaleRepository;

final class AddCreditServiceTest extends TestCase
{
    private AddCreditService $addCreditService;
    private SaleRepository|MockObject $saleRepository;

    protected function setUp(): void
    {
        $this->saleRepository = $this->createRepository();
        $this->addCreditService = new AddCreditService($this->saleRepository);

        parent::setUp();
    }

    #[Test]
    #[DataProvider('validCommands')]
    public function add_withValidCommand_returnsSuccessfulResultWithPositiveCredit(AddCreditCommand $command): void
    {
        $result = $this->addCreditService->add($command);

        self::assertTrue($result->isSuccess());
        self::assertTrue($result->getCredit() > 0);
    }

    public static function validCommands(): array
    {
        $validCoins = [0.05, 0.10, 0.25, 1.0];
        $faker = Factory::create();

        $commands = [];
        foreach ($validCoins as $coin) {
            $commands[] = [new AddCreditCommand($coin, null)];
            $commands[] = [new AddCreditCommand($coin, $faker->uuid())];
        }

        return $commands;
    }

    private function createRepository(): SaleRepository|MockObject
    {
        $saleRepository = $this->createMock(SaleRepository::class);

        $saleRepository
            ->method('findOrCreateNewSale')
            ->willReturn(SaleBuilder::aNewlyCreatedSale());

        return $saleRepository;
    }
}
