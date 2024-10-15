<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinStock;
use VendingMachine\Common\Infrastructure\Outbound\Persistence\InMemoryChangeStockControl;
use VendingMachine\Operation\Domain\InsufficientChangeException;
use VendingMachine\Operation\Domain\Model\Sale\Credit;
use VendingMachine\Operation\Domain\Service\ChangeDispenser;

final class ChangeDispenserTest extends TestCase
{
    #[Test]
    #[DataProvider('change')]
    public function dispense_withAvailableCoins_shouldReturnCorrectChange(float $credit, array $availableCoins, array $expectedChange): void
    {
        $changeDispenser = $this->createChangeDispenser($availableCoins);

        $change = $changeDispenser->dispense(new Credit($credit));

        self::assertEquals($expectedChange, $change->toArray());
    }

    #[Test]
    public function dispense_withNotEnoughAvailableCoins_shouldThrowAnInsufficientChangeException(): void
    {
        $changeCalculator = $this->createChangeDispenser(['1.00' => 0, '0.25' => 1, '0.10' => 0, '0.05' => 0]);

        assertThrows(
            InsufficientChangeException::class,
            fn() => $changeCalculator->dispense(new Credit(1.0))
        );
    }

    public static function change(): array
    {
        return [
            [1.00, ['1.00' => 5, '0.25' => 10, '0.10' => 5, '0.05' => 10], [new Coin(1.00)]],
            [0.65, ['1.00' => 5, '0.25' => 10, '0.10' => 5, '0.05' => 10], [new Coin(0.25), new Coin(0.25), new Coin(0.10), new Coin(0.05)]],
            [0.65, ['1.00' => 1, '0.25' =>  1, '0.10' => 5, '0.05' => 10], [new Coin(0.25), new Coin(0.10), new Coin(0.10), new Coin(0.10), new Coin(0.10)]],
            [0.00, ['1.00' => 5, '0.25' => 10, '0.10' => 5, '0.05' => 10], []],
        ];
    }

    private function createChangeDispenser(array $availableCoins): ChangeDispenser
    {
        $changeStockControl = new class($availableCoins) extends InMemoryChangeStockControl {
            public function __construct(array $availableCoins)
            {
                $map = [
                    '0.05' => '4',
                    '0.10' => '3',
                    '0.25' => '2',
                    '1.00' => '1',
                ];

                $coins = [];
                foreach ($availableCoins as $value => $quantity) {
                    $id = $map[$value];
                    $coins[(string) $id] = new CoinStock((string) $id, new Coin((float) $value), $quantity);
                }

                parent::__construct($coins);
            }
        };

        return new ChangeDispenser($changeStockControl);
    }
}
