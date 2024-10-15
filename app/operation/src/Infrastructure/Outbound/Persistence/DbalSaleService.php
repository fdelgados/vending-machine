<?php declare(strict_types=1);

namespace VendingMachine\Operation\Infrastructure\Outbound\Persistence;

use ReflectionClass;
use VendingMachine\Common\Domain\Coin;
use VendingMachine\Common\Domain\CoinCollection;
use VendingMachine\Common\Domain\ProductId;
use VendingMachine\Lib\Doctrine\DbalService;
use VendingMachine\Operation\Domain\Model\Sale\Credit;
use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Domain\Model\Sale\SaleState;

final class DbalSaleService extends DbalService implements SaleRepository
{
    public function save(Sale $sale): void
    {
        $sql = 'INSERT INTO sales (id, coins, credit, state, product_id)
            VALUES (:id, :coins, :credit, :state, :product_id)
            ON DUPLICATE KEY UPDATE
            coins = VALUES(coins),
            credit = VALUES(credit),
            state = VALUES(state),
            product_id = VALUES(product_id)';

        $coins = $sale->getAvailableCoins()->map(fn (Coin $coin) => $coin->getAmount())->toArray();

        $this->connection->executeStatement($sql, [
            'id' => $sale->getId()->getValue(),
            'coins' => json_encode($coins),
            'credit' => $sale->getCredit()->getAmount(),
            'state' => $sale->getState()->name,
            'product_id' => $sale->getProductId()?->value(),
        ]);
    }

    public function saleOfId(SaleId $saleId): ?Sale
    {
        $row = $this->getQueryBuilder()->select('*')
            ->from('sales')
            ->where('id = :id')
            ->setParameter('id', $saleId->getValue())
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function nextIdentity(): SaleId
    {
        return SaleId::generate();
    }

    private function hydrate(array $data): Sale
    {
        $reflectionClass = new ReflectionClass(Sale::class);

        $sale = $reflectionClass->newInstanceWithoutConstructor();

        $reflectionId = $reflectionClass->getProperty('id');
        $reflectionId->setValue($sale, SaleId::fromString($data['id']));

        $coins = json_decode($data['coins'], true);
        $coins = array_map(fn ($coin) => new Coin((float) $coin), $coins);
        $reflectionCoins = $reflectionClass->getProperty('coins');
        $reflectionCoins->setValue($sale, new CoinCollection(...$coins));

        $reflectionCredit = $reflectionClass->getProperty('credit');
        $reflectionCredit->setValue($sale, new Credit((float) $data['credit']));

        $reflectionState = $reflectionClass->getProperty('state');
        $reflectionState->setValue($sale, SaleState::fromName($data['state']));

        $reflectionProductId = $reflectionClass->getProperty('productId');
        $productId = $data['product_id'] ? new ProductId($data['product_id']) : null;
        $reflectionProductId->setValue($sale, $productId);

        return $sale;
    }
}
