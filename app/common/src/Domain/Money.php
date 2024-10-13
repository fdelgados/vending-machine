<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

interface Money extends \Stringable
{
    public function getAmount(): float;
}
