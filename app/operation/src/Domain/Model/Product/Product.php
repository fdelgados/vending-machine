<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Product;

final class Product
{
    private readonly ProductId $id;
    private Name $name;
    private Price $price;
    private int $availableStock;

    public function __construct(ProductId $id, Name $name, Price $price, int $availableStock)
    {
        ensure($availableStock >= 0, 'Available stock must be greater than or equal to 0');

        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->availableStock = $availableStock;
    }

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function isOutOfStock(): bool
    {
        return $this->availableStock === 0;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }

    public function decreaseStock(): void
    {
        precondition($this->availableStock > 0, 'Product is out of stock');

        $this->availableStock--;
    }
}
