<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use Faker\Factory;
use VendingMachine\Operation\Domain\Model\Sale\Coin;

final class CoinBuilder
{
    private float $value;

    private function __construct()
    {
        $faker = Factory::create();

        $this->value = $faker->randomElement([0.05, 0.10, 0.25, 1.0]);
    }

    public static function aCoin(): self
    {
        return new self();
    }

    public function ofValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function build(): Coin
    {
        return new Coin($this->value);
    }
}
