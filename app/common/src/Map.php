<?php declare(strict_types=1);

namespace VendingMachine\Common;

/**
 * Class Map<string|int, T>
 *
 * @template T
 */
abstract class Map extends Sequence
{
    public function __construct(array $items = [])
    {
        ensure($this->typesAreExpected(...$items), 'All items must be of type ' . $this->getType());

        $this->items = $items;
    }

    /** @param T $value */
    public function add(string $key, mixed $value): void
    {
        if (!$this->isOfExpectedType($value)) {
            throw new \UnexpectedValueException(
                sprintf('Value of type %s is not of expected type %s', gettype($value), $this->getType())
            );
        }

        $this->items[$key] = $value;
    }

    public function map(callable $fn): Map
    {
        $items = [];

        foreach ($this->items as $key => $value) {
            $items[$key] = $fn($value, $key);
        }

        $type = $items ? gettype(array_values($items)[0]) : $this->getType();

        return new class($type, $items) extends Map {
            public function __construct(private readonly string $type, $items)
            {
                parent::__construct($items);
            }

            protected function getType(): string
            {
                return $this->type;
            }
        };
    }

    public function each(callable $fn): void
    {
        foreach ($this->items as $key => $value) {
            $fn($value, $key);
        }
    }
}
