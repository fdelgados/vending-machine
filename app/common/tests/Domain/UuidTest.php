<?php declare(strict_types=1);

namespace Tests\VendingMachine\Common\Domain;

use Faker\Factory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VendingMachine\Common\Domain\Uuid;

final class UuidTest extends TestCase
{
    private const string VALID_UUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';
    private const string NULL_UUID = '00000000-0000-0000-0000-000000000000';

    #[Test]
    public function generate_createsAValidUuid(): void
    {
        $uuid = Uuid::generate();

        self::assertMatchesRegularExpression(self::VALID_UUID_REGEX, $uuid->getValue());
    }

    #[Test]
    public function ofNullable_withNullValue_createsANullUuid(): void
    {
        $uuid = Uuid::ofNullable(null);

        self::assertTrue($uuid->isNull());
        self::assertEquals(self::NULL_UUID, $uuid->getValue());
    }

    #[Test]
    public function ofNullable_withValidUuidValue_createsAValidUuid(): void
    {
        $faker = Factory::create();
        $uuid = Uuid::ofNullable($faker->uuid());

        self::assertFalse($uuid->isNull());
        self::assertNotEquals(self::NULL_UUID, $uuid->getValue());
    }

    #[Test]
    public function fromString_withValidUuidValue_createsAValidUuid(): void
    {
        $faker = Factory::create();
        $uuid = Uuid::fromString($faker->uuid());

        self::assertFalse($uuid->isNull());
        self::assertNotEquals(self::NULL_UUID, $uuid->getValue());
    }

    #[Test]
    public function fromString_withInvalidUuidValue_throwsAnInvalidArgumentException(): void
    {
        assertThrows(
            InvalidArgumentException::class,
            fn() => Uuid::fromString('invalid')
        );
    }
}
