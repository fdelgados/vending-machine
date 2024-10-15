<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\ListProducts;

use VendingMachine\Common\Map;

final class ProductMap extends Map
{
    protected function getType(): string
    {
        return ProductDto::class;
    }
}
