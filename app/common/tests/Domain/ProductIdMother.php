<?php declare(strict_types=1);

namespace Tests\VendingMachine\Common\Domain;

use Faker\Factory;
use VendingMachine\Common\Domain\ProductId;

final class ProductIdMother
{
    public static function random(): ProductId
    {
        $faker = Factory::create();

        return new ProductId((string) $faker->randomDigitNot(0));
    }

    public static function oneOf(string ...$ids): ProductId
    {
        $faker = Factory::create();

        return ProductIdMother::ofId($faker->randomElement($ids));
    }

    public static function ofId(string $id): ProductId
    {
        return new ProductId($id);
    }
}
