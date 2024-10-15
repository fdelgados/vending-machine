<?php declare(strict_types=1);

namespace VendingMachine\Common\Application\ListProducts;

use VendingMachine\Lib\Map;

final class ProductMap extends Map
{
    protected function getType(): string
    {
        return ProductDto::class;
    }
}
