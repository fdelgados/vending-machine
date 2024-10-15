<?php declare(strict_types=1);

namespace Tests\VendingMachine\Common\Domain;

use Faker\Factory;
use VendingMachine\Common\Domain\Product\Name;

final class ProductNameMother
{
    public static function random(): Name
    {
        $faker = Factory::create();

        return new Name($faker->word());
    }
}
