<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Application\AddCredit;

use Faker\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VendingMachine\Operation\Application\AddCredit\AddCreditCommand;
use VendingMachine\Operation\Application\AddCredit\AddCreditService;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\InMemorySaleRepository;

final class AddCreditServiceTest extends TestCase
{
    private AddCreditService $addCreditService;

    protected function setUp(): void
    {
        $saleRepository = new InMemorySaleRepository();
        $this->addCreditService = new AddCreditService($saleRepository);

        parent::setUp();
    }

    #[Test]
    #[DataProvider('validCommands')]
    public function add_withValidCommand_returnsSuccessfulResultWithPositiveCredit(AddCreditCommand $command): void
    {
        $result = $this->addCreditService->add($command);

        self::assertTrue($result->isSuccess());
        self::assertIsFloat($result->getValue());
        self::assertGreaterThan(0.0, $result->getValue());
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
