<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain;

final class InsufficientChangeException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Insufficient change');
    }
}
