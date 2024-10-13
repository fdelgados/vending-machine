<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Sale;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VendingMachine\Operation\Domain\Model\Sale\Credit;

final class CreditTest extends TestCase
{
    #[Test]
    #[DataProvider('negatives')]
    public function credit_withNegativeValue_throwsInvalidArgumentException(float $value): void
    {
        assertThrows(
            \InvalidArgumentException::class,
            fn () => new Credit($value)
        );
    }

    #[Test]
    public function isPositive_forPositiveValues_returnsTrue(): void
    {
        $credit = new Credit(0.001);

        self::assertTrue($credit->isPositive());
    }

    #[Test]
    public function isPositive_forZero_returnsFalse(): void
    {
        $zeroCredit = Credit::zero();

        self::assertFalse($zeroCredit->isPositive());
    }

    #[Test]
    public function isPositive_withResultsOfSubtractSameSmallValues_returnsFalse(): void
    {
        $credit = new Credit(0.05);
        $zeroCredit = $credit->minus(new Credit(0.05));

        self::assertFalse($zeroCredit->isPositive());
    }

    #[Test]
    public function minus_withSameSmallValues_returnsZero(): void
    {
        $credit = new Credit(0.05);
        $zeroCredit = $credit->minus(new Credit(0.05));

        self::assertSame(0.0, $zeroCredit->getAmount());
    }

    #[Test]
    public function plus_withSmallAmounts_sumsCorrectly(): void
    {
        $credit = new Credit(0.05);
        $sumCredit = $credit->plus(new Credit(0.05));

        self::assertSame(0.10, $sumCredit->getAmount());
    }

    #[Test]
    public function equals_withSameSmallAmounts_returnsTrue(): void
    {
        $credit = new Credit(0.05);
        $otherCredit = new Credit(0.05);

        self::assertTrue($credit->equals($otherCredit));
    }

    public static function negatives(): array
    {
        return [
            [-1.0],
            [-0.00001]
        ];
    }
}
