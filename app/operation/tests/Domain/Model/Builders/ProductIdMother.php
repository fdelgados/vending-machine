<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use Faker\Factory;
use VendingMachine\Operation\Domain\Model\Product\ProductId;

final class ProductIdMother
{
    public static function random(): ProductId
    {
        $faker = Factory::create();

        return new ProductId((string) $faker->randomDigitNot(0));
    }

    public static function ofId(string $id): ProductId
    {
        return new ProductId($id);
    }
}
