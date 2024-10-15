<?php declare(strict_types=1);

namespace VendingMachine\Lib;

/**
 * Class Error
 *
 * This class represents an error that can occur during an operation. It holds an error code that can be used
 * to identify the type of error.
 */
final class Error implements \Stringable
{
    /**
     * @var string|null The description of the error.
     */
    private ?string $description;

    /**
     * Error constructor.
     *
     * @param string $code The error code.
     * @param string|null $description The description of the error.
     */
    private function __construct(private readonly string $code, ?string $description = null)
    {
        $this->description = $description;
    }

    /**
     * Creates an Error from a given error code.
     *
     * @param string $code The error code.
     *
     * @return Error An Error with the given error code.
     */
    public static function ofCode(string $code): Error
    {
        return new self($code);
    }

    /**
     * Creates an Error with a given code and a description.
     *
     * @param string $code The error code.
     * @param string $description The description of the error.
     *
     * @return Error An Error with the given code and description.
     */
    public static function withDescription(string $code, string $description): Error
    {
        return new self($code, $description);
    }

    /**
     * Gets the description of the Error.
     *
     * @return string The description of the Error.
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Checks if the error code of the Error matches a given error code.
     *
     * @param string $code The error code to check.
     * @return bool True if the error code of the Error matches the given error code, false otherwise.
     */
    public function is(string $code): bool
    {
        return $this->code === $code;
    }

    /**
     * Gets the error code of the Error.
     *
     * @return string The error code of the Error.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Converts the Error to a string.
     *
     * @return string A string representation of the Error.
     */
    public function __toString(): string
    {
        return $this->getCode();
    }
}
