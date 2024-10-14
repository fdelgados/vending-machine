<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

use VendingMachine\Common\Collection;

final class CoinCollection extends Collection
{
    public function __construct(Coin ...$coins)
    {
        parent::__construct(...$coins);
    }

    protected function getType(): string
    {
        return Coin::class;
    }
}
