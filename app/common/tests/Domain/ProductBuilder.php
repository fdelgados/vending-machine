<?php declare(strict_types=1);

namespace Tests\VendingMachine\Common\Domain;

use Faker\Factory;
use Faker\Generator;
use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Common\Domain\Product\Name;
use VendingMachine\Common\Domain\Product\Price;
use VendingMachine\Common\Domain\Product\Product;

final class ProductBuilder
{
    private readonly Generator $faker;

    private readonly ProductId $id;
    private Name $name;
    private Price $price;
    private int $remainingStock;

    private function __construct()
    {
        $this->faker = Factory::create();

        $this->id = ProductIdMother::random();
        $this->name = ProductNameMother::random();
        $this->price = PriceMother::random();
        $this->remainingStock = $this->faker->numberBetween(0, 100);
    }

    public static function aProduct(): self
    {
        return new self();
    }

    public function pricedAt(Price $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function withAvailableStock(): self
    {
        $this->remainingStock = $this->faker->numberBetween(1, 100);

        return $this;
    }

    public function outOfStock(): self
    {
        $this->remainingStock = 0;

        return $this;
    }

    public function build(): Product
    {
        return new Product($this->id, $this->name, $this->price, $this->remainingStock);
    }
}
