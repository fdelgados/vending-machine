<?php declare(strict_types=1);

namespace VendingMachine\Common;

use Countable;
use IteratorAggregate;
use Traversable;

abstract class Collection implements Countable, IteratorAggregate
{
    private array $items;

    public function __construct(mixed ...$items)
    {
        ensure($this->typesAreExpected(...$items), 'All items must be of type ' . $this->getType());

        $this->items = $items;
    }

    private function typesAreExpected(mixed ...$items): bool
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

    public function map(callable $fn): Collection
    {
        $items = array_map($fn, $this->items);

        $type = $items ? gettype($items[0]) : $this->getType();

        return new class($type, ...$items) extends Collection {
            public function __construct(private readonly string $type, ...$items)
            {
                parent::__construct(...$items);
            }

            protected function getType(): string
            {
                return $this->type;
            }
        };
    }

    public function each(callable $fn): void
    {
        foreach ($this->items as $item) {
            $fn($item);
        }
    }

    public function add(mixed $value): void
    {
        if (!$this->isOfExpectedType($value)) {
            throw new \UnexpectedValueException(
                sprintf('Value of type %s is not of expected type %s', gettype($value), $this->getType())
            );
        }

        $this->items[] = $value;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !empty($this->items);
    }

    private function isOfExpectedType(mixed $value): bool
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
}
