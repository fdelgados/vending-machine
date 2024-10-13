<?php declare(strict_types=1);

namespace VendingMachine\Operation\Domain\Model\Product;

final readonly class ProductId implements \Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        ensure(is_numeric($value), 'Product ID must be a numeric value');
        ensure((int) $value > 0, 'Product ID must be greater than 0');

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
