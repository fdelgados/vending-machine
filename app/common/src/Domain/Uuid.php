<?php declare(strict_types=1);

namespace VendingMachine\Common\Domain;

use Ramsey\Uuid\Uuid as RamseyUuid;

readonly class Uuid implements \Stringable
{
    private const string UUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';
    private const string NULL_UUID = '00000000-0000-0000-0000-000000000000';

    private string $value;

    private function __construct(string $value)
    {
        ensure($this->isValidUUID($value), 'Identifier is not a valid UUID');

        $this->value = $value;
    }

    private function isValidUUID(string $value): bool
    {
        return preg_match(self::UUID_REGEX, $value) === 1;
    }

    final public static function generate(): static
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    final public static function fromString(string $value): static
    {
        return new static($value);
    }

    final public static function ofNullable(?string $value): static
    {
        $value = $value ?? self::NULL_UUID;

        return new static($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isNull(): bool
    {
        return $this->value === self::NULL_UUID;
    }

    public function equals(Uuid $identifier): bool
    {
        return $this->value === $identifier->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
