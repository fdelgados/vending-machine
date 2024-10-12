<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Product;

final readonly class ProductId implements \Stringable
{
    private int $value;

    public function __construct(int $value)
    {
        ensure($value > 0, 'Product ID must be greater than 0');

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
