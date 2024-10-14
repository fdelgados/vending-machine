<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\AddCredit;

use Faker\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\VendingMachine\Common\Domain\CoinBuilder;
use VendingMachine\Common\Domain\ChangeDispenser;
use VendingMachine\Common\Infrastructure\Outbound\InMemoryChangeDispenser;
use VendingMachine\Operation\Application\AddCredit\AddCreditCommand;
use VendingMachine\Operation\Application\AddCredit\AddCreditService;
use VendingMachine\Operation\Domain\Model\Sale\Credit;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemorySaleRepository;

final class AddCreditServiceTest extends TestCase
{
    private AddCreditService $addCreditService;
    private ChangeDispenser $changeDispenser;

    protected function setUp(): void
    {
        $saleRepository = new InMemorySaleRepository();
        $this->changeDispenser = new InMemoryChangeDispenser();

        $this->addCreditService = new AddCreditService($saleRepository, $this->changeDispenser);

        parent::setUp();
    }

    #[Test]
    #[DataProvider('validCommands')]
    public function add_withValidCommand_returnsSuccessfulResultWithPositiveCredit(AddCreditCommand $command): void
    {
        $result = $this->addCreditService->add($command);

        self::assertTrue($result->isSuccess());
        self::assertInstanceOf(Credit::class, $result->getValue());
        self::assertTrue($result->getValue()->isGreaterThan(Credit::zero()));
    }

    #[Test]
    #[DataProvider('validCommands')]
    public function add_withValidCommand_increasesTheChangeDispenserStock(AddCreditCommand $command): void
    {
        $coin = CoinBuilder::aCoin()->ofValue($command->getCoinValue())->build();
        $stockBefore = $this->changeDispenser->getStockOfCoin($coin);

        $this->addCreditService->add($command);

        $stockAfter = $this->changeDispenser->getStockOfCoin($coin);

        self::assertTrue($stockAfter > $stockBefore);
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
}
