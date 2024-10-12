<?php

namespace VendingMachine\Operation\Domain\Model;

enum SaleState
{
    case IN_PROGRESS;
    case CANCELLED;
    case COMPLETED;

    public function isInProgress(): bool
    {
        return $this === self::IN_PROGRESS;
    }
}
