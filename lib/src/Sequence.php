<?php declare(strict_types=1);

namespace VendingMachine\Lib;

use Countable;
use IteratorAggregate;
use Stringable;
use Traversable;

/**
 * Abstract class Sequence
 *
 * This class provides a base for collections of items, implementing common
 * interfaces for counting, iterating, and string representation.
 */
abstract class Sequence implements Countable, IteratorAggregate, Stringable
{
    /**
     * @var array The items in the sequence.
     */
    protected array $items;

    /**
     * Checks if all items are of the expected type.
     *
     * @param mixed ...$items The items to check.
     * @return bool True if all items are of the expected type, false otherwise.
     */
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

    /**
     * Gets the expected type of the items in the sequence.
     *
     * @return string The expected type of the items.
     */
    abstract protected function getType(): string;

    /**
     * Gets an iterator for the items in the sequence.
     *
     * @return Traversable An iterator for the items.
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Converts the sequence to an array.
     *
     * @return array The items in the sequence as an array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Applies a callback function to each item in the sequence and returns a new sequence.
     *
     * @param callable $fn The callback function to apply.
     * @return Sequence A new sequence with the transformed items.
     */
    abstract public function map(callable $fn): Sequence;

    /**
     * Applies a callback function to each item in the sequence.
     *
     * @param callable $fn The callback function to apply.
     */
    abstract public function each(callable $fn): void;

    /**
     * Counts the number of items in the sequence.
     *
     * @return int The number of items.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Checks if the sequence is not empty.
     *
     * @return bool True if the sequence is not empty, false otherwise.
     */
    public function isNotEmpty(): bool
    {
        return !empty($this->items);
    }

    /**
     * Checks if the sequence is empty.
     *
     * @return bool True if the sequence is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Checks if a value is of the expected type.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is of the expected type, false otherwise.
     */
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

    /**
     * Converts the sequence to a string.
     *
     * @return string The string representation of the sequence.
     */
    public function __toString(): string
    {
        return implode(', ', array_values($this->items));
    }
}
