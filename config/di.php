<?php declare(strict_types=1);

use VendingMachine\Common\Domain\ChangeStockControl;
use VendingMachine\Common\Infrastructure\Outbound\DbalChangeStockControl;
use VendingMachine\Operation\Domain\Model\Product\ProductRepository;
use VendingMachine\Operation\Domain\Model\Sale\SaleRepository;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\DbalSaleService;
use VendingMachine\Operation\Infrastructure\Outbound\Persistence\DbalProductService;
use function DI\autowire;

return [
    SaleRepository::class => autowire(DbalSaleService::class),
    ChangeStockControl::class => autowire(DbalChangeStockControl::class),
    ProductRepository::class => autowire(DbalProductService::class),
];
