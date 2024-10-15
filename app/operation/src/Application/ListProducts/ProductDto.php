<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\ListProducts;

final readonly class ProductDto implements \Stringable
{
    public function __construct(private string $id, private string $name, private float $price)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name, number_format($this->price, 2));
    }
}
