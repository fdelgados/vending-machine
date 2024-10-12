<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use Faker\Factory;
use VendingMachine\Operation\Domain\Model\Product\ProductId;

final class ProductIdMother
{
    public static function random(): ProductId
    {
        $faker = Factory::create();

        return new ProductId($faker->randomDigitNot(0));
    }
}
