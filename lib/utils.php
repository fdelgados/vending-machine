<?php declare(strict_types=1);

if (!function_exists('ensure')) {
    /**
     * Ensures that a condition is true.
     *
     * This function is used to enforce preconditions that must be true.
     * It is useful for validating value object's input parameters.
     * If the condition is false, an exception is thrown.
     *
     * @param bool        $condition The condition that must be true.
     * @param string|null $message   Description of the condition.
     * @throws InvalidArgumentException If the condition is false.
     */
    function ensure(bool $condition, ?string $message = null): void
    {
        if (!$condition) {
            throw new InvalidArgumentException($message ?? 'Invalid value');
        }
    }
}

if (!function_exists('precondition')) {
    /**
     * Ensures that a condition is true.
     *
     * This function is used to enforce preconditions that must be true.
     * It is useful for validating that a method contract is respected.
     * If the condition is false, an exception is thrown.
     *
     * @param bool        $condition The condition that must be true.
     * @param string|null $message   Description of the condition.
     * @throws DomainException If the condition is false.
     */
    function precondition(bool $condition, ?string $message = null): void
    {
        if (!$condition) {
            throw new DomainException($message ?? 'Invalid value');
        }
    }
}
