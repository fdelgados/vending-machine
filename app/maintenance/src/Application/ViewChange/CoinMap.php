<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Application\ViewChange;

use VendingMachine\Common\Map;

final class CoinMap extends Map
{
    protected function getType(): string
    {
        return CoinDto::class;
    }
}
