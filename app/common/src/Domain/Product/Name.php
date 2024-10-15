<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain\Product;

final readonly class Name implements \Stringable
{
    private string $value;

    public function __construct(string $name)
    {
        ensure($name !== '', 'The name of the product cannot be empty.');
        ensure(strlen($name) <= 255, 'The name of the product cannot be longer than 255 characters.');

        $this->value = $name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
