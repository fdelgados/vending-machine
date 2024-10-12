<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use Faker\Factory;
use VendingMachine\Operation\Domain\Model\Product\Name;

final class ProductNameMother
{
    public static function random(): Name
    {
        $faker = Factory::create();

        return new Name($faker->word());
    }
}
