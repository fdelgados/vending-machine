<?php declare(strict_types=1);

namespace VendingMachine\Lib;

use Countable;
use IteratorAggregate;
use Stringable;
use Traversable;

abstract class Sequence implements Countable, IteratorAggregate, Stringable
{
    protected array $items;

    protected function typesAreExpected(mixed ...$items): bool
    {
        if (empty($items)) {
            return true;
        }

        return array_reduce(
            $items,
            fn($carry, $item) => $carry && $this->isOfExpectedType($item),
            true
        );
    }

    abstract protected function getType(): string;

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    abstract public function map(callable $fn): Sequence;

    abstract public function each(callable $fn): void;

    public function count(): int
    {
        return count($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !empty($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    protected function isOfExpectedType(mixed $value): bool
    {
        $type = $this->getType();

        return match ($type) {
            'array' => is_array($value),
            'bool', 'boolean' => is_bool($value),
            'callable' => is_callable($value),
            'float', 'double' => is_float($value),
            'int', 'integer' => is_int($value),
            'null' => $value === null,
            'numeric' => is_numeric($value),
            'object' => is_object($value),
            'resource' => is_resource($value),
            'scalar' => is_scalar($value),
            'string' => is_string($value),
            'mixed' => true,
            default => $value instanceof $type,
        };
    }

    public function __toString(): string
    {
        return implode(', ', array_values($this->items));
    }
}
