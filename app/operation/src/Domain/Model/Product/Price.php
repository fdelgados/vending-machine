<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Product;

use VendingMachine\Common\Domain\Money;

final class Price extends Money
{
    public function __construct(float $amount)
    {
        ensure($amount > 0.0, 'Price must be greater than 0');

        $this->amount = $amount;
    }
}
