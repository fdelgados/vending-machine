<?php declare(strict_types=1);

namespace VendingMachine\Operation\Infrastructure\Outbound\Persistence;

use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Operation\Domain\Model\Product\Name;
use VendingMachine\Operation\Domain\Model\Product\Price;
use VendingMachine\Operation\Domain\Model\Product\Product;
use VendingMachine\Operation\Domain\Model\Product\ProductRepository;

final class InMemoryProductRepository implements ProductRepository
{
    private array $products;

    public function __construct()
    {
        $this->products = [
            '1' => new Product(new ProductId('1'), new Name('Water'), new Price(0.65), 100),
            '2' => new Product(new ProductId('2'), new Name('Juice'), new Price(1.00), 100),
            '3' => new Product(new ProductId('3'), new Name('Soda'), new Price(1.50), 100),
        ];
    }

    public function productOfId(ProductId $productId): ?Product
    {
        return $this->products[$productId->value()] ?? null;
    }

    public function save(Product $product): void
    {
        $this->products[$product->getId()->value()] = $product;
    }

    public function findAll(): array
    {
        return $this->products;
    }
}
