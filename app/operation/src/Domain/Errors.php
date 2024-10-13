<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain;

use VendingMachine\Common\Error;

final class Errors
{
    public static function saleNotFound(): Error
    {
        return Error::withDescription('sale_not_found', 'Sale not found');
    }

    public static function insufficientCredit(): Error
    {
        return Error::withDescription('insufficient_credit', 'Insufficient credit');
    }

    public static function productOutOfStock(): Error
    {
        return Error::withDescription('product_out_of_stock', 'Product out of stock');
    }

    public static function productNotFound(): Error
    {
        return Error::withDescription('product_not_found', 'Product not found');
    }

    public static function notEnoughChange(): Error
    {
        return Error::withDescription('not_enough_change', 'Not enough change');
    }
}
