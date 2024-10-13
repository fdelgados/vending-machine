<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinStock;
use VendingMachine\Operation\Domain\Model\Sale\Credit;
use VendingMachine\Operation\Domain\Service\ChangeCalculator;

final class ChangeCalculatorTest extends TestCase
{
    #[Test]
    #[DataProvider('change')]
    public function calculate_withAvailableCoins_shouldReturnCorrectChange(float $credit, array $availableCoins, array $expectedChange): void
    {
        $changeCalculator = $this->createChangeCalculator($availableCoins);

        $result = $changeCalculator->calculate(new Credit($credit));

        self::assertEquals($expectedChange, $result->getValue());
    }

    #[Test]
    public function calculate_withNotEnoughAvailableCoins_shouldReturnAFailureResult(): void
    {
        $changeCalculator = $this->createChangeCalculator(['1.00' => 0, '0.25' => 1, '0.10' => 0, '0.05' => 0]);

        $result = $changeCalculator->calculate(new Credit(1.0));

        self::assertTrue($result->isFailure());
        self::assertEquals('not_enough_change', $result->getErrorCode());
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

    private function createChangeCalculator(array $availableCoins): ChangeCalculator
    {
        return new class($availableCoins) extends ChangeCalculator {
            private array $availableCoins = [];

            public function __construct(array $availableCoins)
            {
                foreach ($availableCoins as $value => $quantity) {
                    $this->availableCoins[$value] = new CoinStock(new Coin((float) $value), $quantity);
                }
            }

            protected function getAvailableCoins(): array
            {
                return $this->availableCoins;
            }
        };
    }
}
