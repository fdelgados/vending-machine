<?php declare(strict_types=1);

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Domain\Product\ProductRepository;
use VendingMachine\Common\Infrastructure\Outbound\Persistence\DbalChangeStockControl;
use VendingMachine\Common\Infrastructure\Outbound\Persistence\DbalProductRepository;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\DbalSaleService;
use function DI\autowire;

return [
    SaleRepository::class => autowire(DbalSaleService::class),
    ChangeStockControl::class => autowire(DbalChangeStockControl::class),
    ProductRepository::class => autowire(DbalProductRepository::class),
];
