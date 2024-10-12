<?php declare(strict_types=1);

namespace VendingMachine\Common;

use Throwable;

/**
 * Class Result
 *
 * @template T
 *
 * This class represents the result of an operation. It can either be a success or a failure.
 * In case of a success, it can optionally hold a value. In case of a failure, it holds an Error.
 */
class Result implements \Stringable
{
    private bool $isSuccess;
    private ?Error $error;

    /** @var T */
    private mixed $value;

    /**
     * Result constructor.
     *
     * @param bool $isSuccess Indicates whether the operation was a success.
     * @param Error|null $error The error of the operation, if it was a failure.
     * @param mixed $value The value of the operation, if it was a success.
     */
    final protected function __construct(bool $isSuccess, ?Error $error = null, mixed $value = null)
    {
        assert(($isSuccess && is_null($error)) || (!$isSuccess && !is_null($error)));

        $this->value = $value;
        $this->isSuccess = $isSuccess;
        $this->error = $error;
    }

    /**
     * Creates a successful Result with an optional value.
     *
     * @param T $value The value of the operation.
     * @return static A successful Result.
     */
    final public static function success(mixed $value = null): static
    {
        return new static(true, null, $value);
    }

    /**
     * Creates a failed Result with an Error.
     *
     * @param Error $error The error of the operation.
     *
     * @return self A failed Result.
     */
    final public static function failure(Error $error): static
    {
        return new static(false, $error);
    }

    /**
     * Checks if the Result is a success.
     *
     * @return bool True if the Result is a success, false otherwise.
     */
    final public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * Checks if the Result is a failure.
     *
     * @return bool True if the Result is a failure, false otherwise.
     */
    final public function isFailure(): bool
    {
        return !$this->isSuccess && !is_null($this->error);
    }

    /**
     * Gets the error code of the Result.
     *
     * @return string The error code if the Result is a failure, an empty string otherwise.
     */
    final public function getErrorCode(): string
    {
        if (is_null($this->error)) {
            return '';
        }

        return $this->isFailure() ? $this->error->getCode() : '';
    }

    /**
     * Gets the value of the Result.
     *
     * @return T The value or values of the Result.
     */
    final public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Gets the value of the Result or throws an exception if the Result is a failure.
     *
     * @return T The value or values of the Result.
     *           If the Result is a failure, an exception is thrown.
     * @throws Throwable
     */
    final public function getOrThrow(Throwable $throwable): mixed
    {
        if ($this->isSuccess) {
            return $this->value;
        }

        throw $throwable;
    }

    /**
     * Matches the Result to a success or failure function.
     *
     * @param callable $success The function to call if the Result is a success.
     * @param callable $failure The function to call if the Result is a failure.
     * @return mixed The return value of the success or failure function.
     */
    final public function match(callable $success, callable $failure): mixed
    {
        if ($this->isSuccess()) {
            return $success($this->getCredit());
        }

        return $failure($this->error);
    }

    /**
     * Checks if the error code of the Result matches a given error code.
     *
     * @param string $errorCode The error code to check.
     * @return bool True if the error code of the Result matches the given error code, false otherwise.
     */
    final public function errorIs(string $errorCode): bool
    {
        if (is_null($this->error)) {
            return false;
        }

        return strcmp($this->error->getCode(), $errorCode) === 0;
    }

    /**
     * Converts the Result to a string.
     *
     * @return string A string representation of the Result.
     */
    public function __toString(): string
    {
        if ($this->isSuccess) {
            return 'Success';
        }

        return sprintf('Fail <%s>', $this->getErrorCode());
    }
}
