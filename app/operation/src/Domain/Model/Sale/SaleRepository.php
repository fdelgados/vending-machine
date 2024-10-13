<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Sale;

interface SaleRepository
{
    public function findOrCreateNewSale(SaleId $saleId): Sale;

    public function save(Sale $sale): void;

    public function saleOfId(SaleId $saleId): ?Sale;

    public function nextIdentity(): SaleId;
}
