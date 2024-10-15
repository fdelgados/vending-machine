<?php declare(strict_types=1);

namespace VendingMachine\Common;

/**
 * Class Collection<T>
 *
 * @template T
 */
abstract class Collection extends Sequence
{
    /** @param T ...$items */
    public function __construct(mixed ...$items)
    {
        ensure($this->typesAreExpected(...$items), 'All items must be of type ' . $this->getType());

        $this->items = $items;
    }

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
             * @param T ...$items
             */
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

    public function merge(Collection $collection): Collection
    {
        return new static(...array_merge($this->items, $collection->items));
    }

    public function each(callable $fn): void
    {
        foreach ($this->items as $item) {
            $fn($item);
        }
    }

    /** @param T $value */
    public function add(mixed $value): void
    {
        if (!$this->isOfExpectedType($value)) {
            throw new \UnexpectedValueException(
                sprintf('Value of type %s is not of expected type %s', gettype($value), $this->getType())
            );
        }

        $this->items[] = $value;
    }
}
