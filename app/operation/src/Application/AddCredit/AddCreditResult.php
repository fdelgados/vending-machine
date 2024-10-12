<?php declare(strict_types=1);

namespace VendingMachine\Operation\Application\AddCredit;

use VendingMachine\Common\Result;

final class AddCreditResult extends Result
{
    public static function ok(float $value): self
    {
        return parent::success($value);
    }

    public function getCredit(): float
    {
        return parent::getValue();
    }
}
