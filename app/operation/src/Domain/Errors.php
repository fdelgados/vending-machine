<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain;

use VendingMachine\Common\Error;

final class Errors
{
    public static function saleNotFound(): Error
    {
        return Error::withDescription('sale_not_found', 'Sale not found');
    }
}
