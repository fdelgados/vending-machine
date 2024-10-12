<?php declare(strict_types=1);

namespace Tests\VendingMachine\Operation\Domain\Model\Builders;

use VendingMachine\Operation\Domain\Model\SaleId;

final class SaleIdMother
{
    public static function random(): SaleId
    {
        return SaleId::generate();
    }
}
