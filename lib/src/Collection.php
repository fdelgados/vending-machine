<?php declare(strict_types=1);

namespace VendingMachine\Lib;

use UnexpectedValueException;

/**
 * Class Collection<T>
 *
 * @template T
 */
abstract class Collection extends Sequence
{
    /**
     * Collection constructor.
     *
     * @param T ...$items The items to be added to the collection.
     */
    public function __construct(mixed ...$items)
    {
        ensure($this->typesAreExpected(...$items), 'All items must be of type ' . $this->getType());

        $this->items = $items;
    }

    /**
     * Applies a callback function to each item in the collection and returns a new collection.
     *
     * @param callable $fn The callback function to apply.
     * @return Collection A new collection with the transformed items.
     */
    public function map(callable $fn): Collection
    {
        $items = array_map($fn, $this->items);

        $type = $items ? gettype($items[0]) : $this->getType();

        /**
         * @template T
         * @extends Collection<T>
         */
        return new class($type, ...$items) extends Collection {
            /**
             * Anonymous class constructor.
             *
             * @param string $type The type of the items.
             * @param T ...$items The items to be added to the collection.
             */
            public function __construct(private readonly string $type, ...$items)
            {
                parent::__construct(...$items);
            }

            /**
             * Gets the type of the items in the collection.
             *
             * @return string The type of the items.
             */
            protected function getType(): string
            {
                return $this->type;
            }
        };
    }

    /**
     * Merges the current collection with another collection.
     *
     * @param Collection $collection The collection to merge with.
     * @return Collection A new collection containing items from both collections.
     */
    public function merge(Collection $collection): Collection
    {
        return new static(...array_merge($this->items, $collection->items));
    }

    /**
     * Applies a callback function to each item in the collection.
     *
     * @param callable $fn The callback function to apply.
     */
    public function each(callable $fn): void
    {
        foreach ($this->items as $item) {
            $fn($item);
        }
    }

    /**
     * Adds a new item to the collection.
     *
     * @param T $value The item to add.
     * @throws UnexpectedValueException if the item is not of the expected type.
     */
    public function add(mixed $value): void
    {
        if (!$this->isOfExpectedType($value)) {
            throw new UnexpectedValueException(
                sprintf('Value of type %s is not of expected type %s', gettype($value), $this->getType())
            );
        }

        $this->items[] = $value;
    }
}
