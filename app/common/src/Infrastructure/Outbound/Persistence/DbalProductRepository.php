<?php declare(strict_types=1);

namespace VendingMachine\Common\Infrastructure\Outbound\Persistence;

use ReflectionClass;
use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Common\Domain\Product\Name;
use VendingMachine\Common\Domain\Product\Price;
use VendingMachine\Common\Domain\Product\Product;
use VendingMachine\Common\Domain\Product\ProductRepository;
use VendingMachine\Lib\Doctrine\DbalService;

final class DbalProductRepository extends DbalService implements ProductRepository
{
    public function productOfId(ProductId $productId): ?Product
    {
        $row = $this->getQueryBuilder()->select('*')
            ->from('products')
            ->where('id = :id')
            ->setParameter('id', $productId->value())
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function save(Product $product): void
    {
        $sql = 'INSERT INTO products (id, name, price, available_stock)
            VALUES (:id, :name, :price, :available_stock)
            ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            price = VALUES(price),
            available_stock = VALUES(available_stock)';

        $this->connection->executeStatement($sql, [
            'id' => $product->getId()->value(),
            'name' => $product->getName()->getValue(),
            'price' => $product->getPrice()->getAmount(),
            'available_stock' => $product->getAvailableStock(),
        ]);
    }

    public function findAll(): array
    {
        $products = [];

        $rows = $this->getQueryBuilder()->select('*')
            ->from('products')
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $products[] = $this->hydrate($row);
        }

        return $products;
    }

    private function hydrate(array $data): Product
    {
        $reflectionClass = new ReflectionClass(Product::class);

        $product = $reflectionClass->newInstanceWithoutConstructor();

        $reflectionId = $reflectionClass->getProperty('id');
        $reflectionId->setValue($product, new ProductId($data['id']));

        $reflectionName = $reflectionClass->getProperty('name');
        $reflectionName->setValue($product, new Name($data['name']));

        $reflectionPrice = $reflectionClass->getProperty('price');
        $reflectionPrice->setValue($product, new Price((float) $data['price']));

        $reflectionAvailableStock = $reflectionClass->getProperty('availableStock');
        $reflectionAvailableStock->setValue($product, (int) $data['available_stock']);

        return $product;
    }
}
