<?php declare(strict_types=1);

namespace VendingMachine\Lib;

use UnexpectedValueException;

/**
 * Class Map<string, T>
 *
 * @template T
 */
abstract class Map extends Sequence
{
    /**
     * Map constructor.
     *
     * @param array<string, T> $items The items to initialize the map with.
     */
    public function __construct(array $items = [])
    {
        ensure($this->typesAreExpected(...$items), 'All items must be of type ' . $this->getType());

        $this->items = $items;
    }

    /**
     * Adds a new item to the map.
     *
     * @param string $key The key for the item.
     * @param T $value The item to add.
     * @throws UnexpectedValueException if the item is not of the expected type.
     */
    public function add(string $key, mixed $value): void
    {
        if (!$this->isOfExpectedType($value)) {
            throw new UnexpectedValueException(
                sprintf('Value of type %s is not of expected type %s', gettype($value), $this->getType())
            );
        }

        $this->items[$key] = $value;
    }

    /**
     * Gets an item from the map by its key.
     *
     * @param string $key The key of the item to retrieve.
     * @return ?T The item associated with the key, or null if the key does not exist.
     */
    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Applies a callback function to each item in the map and returns a new map.
     *
     * @param callable $fn The callback function to apply.
     * @return Map A new map with the transformed items.
     */
    public function map(callable $fn): Map
    {
        $items = [];

        foreach ($this->items as $key => $value) {
            $items[$key] = $fn($value, $key);
        }

        $type = $items ? gettype(array_values($items)[0]) : $this->getType();

        return new class($type, $items) extends Map {
            /**
             * Anonymous class constructor.
             *
             * @param string $type The type of the items.
             * @param array<string|int, T> $items The items to initialize the map with.
             */
            public function __construct(private readonly string $type, $items)
            {
                parent::__construct($items);
            }

            /**
             * Gets the type of the items in the map.
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
     * Applies a callback function to each item in the map.
     *
     * @param callable $fn The callback function to apply.
     */
    public function each(callable $fn): void
    {
        foreach ($this->items as $key => $value) {
            $fn($value, $key);
        }
    }
}
