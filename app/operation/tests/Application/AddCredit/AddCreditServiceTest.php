<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\AddCredit;

use Faker\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Operation\Domain\Model\Builders\SaleIdMother;
use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Infrastructure\Outbound\InMemoryChangeStockControl;
use VendingMachine\Operation\Application\AddCredit\AddCreditCommand;
use VendingMachine\Operation\Application\AddCredit\AddCreditService;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemorySaleRepository;

final class AddCreditServiceTest extends TestCase
{
    private AddCreditService $addCreditService;
    private ChangeStockControl $changeStockControl;

    protected function setUp(): void
    {
        $saleRepository = new InMemorySaleRepository();
        $this->changeStockControl = new InMemoryChangeStockControl();

        $this->addCreditService = new AddCreditService($saleRepository, $this->changeStockControl);

        parent::setUp();
    }

    #[Test]
    #[DataProvider('validCommands')]
    public function add_withValidCommand_returnsSuccessfulResultWithPositiveCredit(AddCreditCommand $command): void
    {
        $result = $this->addCreditService->add($command);

        self::assertTrue($result->getCredit() > 0);
    }

    #[Test]
    #[DataProvider('validCommands')]
    public function add_withValidCommand_increasesTheChangeDispenserStock(AddCreditCommand $command): void
    {
        $stockBefore = $this->changeStockControl->getTotalCoins();

        $this->addCreditService->add($command);

        $stockAfter = $this->changeStockControl->getTotalCoins();

        self::assertTrue($stockAfter > $stockBefore);
    }

    public static function validCommands(): array
    {
        $faker = Factory::create();

        $validCoins = [0.05, 0.10, 0.25, 1.0];
        $coinValues = [];
        $commands = [];

        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $coinValues[] = $faker->randomElement($validCoins);
            }
            $commands[] = [new AddCreditCommand(null, ...$coinValues)];
            $commands[] = [new AddCreditCommand(SaleIdMother::random()->getValue(), ...$coinValues)];
        }

        return $commands;
    }
}
