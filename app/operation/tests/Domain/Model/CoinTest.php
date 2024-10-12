<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VendingMachine\Operation\Domain\Model\Coin;

final class CoinTest extends TestCase
{
    #[Test]
    #[DataProvider('validCoinValues')]
    public function coin_withValidValue_shouldCreateACoin(float $value): void
    {
        $coin = new Coin($value);

        self::assertEquals($value, $coin->getValue());
    }

    #[Test]
    #[DataProvider('invalidCoinValues')]
    public function coin_withInvalidValue_shouldThrowAnInvalidArgumentException(float $value): void
    {
        $createCoin = fn() => new Coin($value);

        assertThrows(\InvalidArgumentException::class, $createCoin);
    }

    public static function validCoinValues(): array
    {
        return [
            [0.05],
            [0.10],
            [0.25],
            [1.00],
        ];
    }

    public static function invalidCoinValues(): array
    {
        return [
            [0.01],
            [0.02],
            [0.20],
            [0.50],
            [2.00],
        ];
    }
}
