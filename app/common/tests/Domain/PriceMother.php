<?php declare(strict_types=1);

namespace Tests\VendingMachine\Common\Domain;

use Faker\Factory;
use VendingMachine\Common\Domain\Product\Price;

final class PriceMother
{
    public static function random(): Price
    {
        $faker = Factory::create();

        return new Price($faker->randomElement([0.65, 1.0, 1.50]));
    }

    public static function ofAmount(float $amount): Price
    {
        return new Price($amount);
    }
}
