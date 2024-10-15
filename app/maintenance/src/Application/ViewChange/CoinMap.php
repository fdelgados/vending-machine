<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\ViewChange;

use VendingMachine\Lib\Map;

final class CoinMap extends Map
{
    protected function getType(): string
    {
        return CoinDto::class;
    }
}
