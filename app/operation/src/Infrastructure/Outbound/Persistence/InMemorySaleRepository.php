<?php declare(strict_types=1);

namespace VendingMachine\Operation\Infrastructure\Outbound\Persistence;

use VendingMachine\Operation\Domain\Model\Sale\Sale;
use VendingMachine\Operation\Domain\Model\Sale\SaleId;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;

final class InMemorySaleRepository implements SaleRepository
{
    /** @var array<Sale> */
    private array $sales;

    public function __construct(Sale ...$sales)
    {
        $this->sales = $sales;
    }

    public function findOrCreateNewSale(SaleId $saleId): Sale
    {
        return $this->sales[$saleId->getValue()] ?? new Sale($this->nextIdentity());
    }

    public function save(Sale $sale): void
    {
        $this->sales[$sale->getId()->getValue()] = $sale;
    }

    public function saleOfId(SaleId $saleId): ?Sale
    {
        return $this->sales[$saleId->getValue()] ?? null;
    }

    public function nextIdentity(): SaleId
    {
        return SaleId::generate();
    }
}
