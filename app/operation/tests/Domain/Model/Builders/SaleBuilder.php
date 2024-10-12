<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use VendingMachine\Operation\Domain\Model\Sale;

final class SaleBuilder
{
    private function __construct()
    {
    }

    public static function aNewlyCreatedSale(): self
    {
        return new self();
    }

    public function build(): Sale
    {
        return new Sale();
    }
}
