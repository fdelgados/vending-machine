<?php declare(strict_types=1);

use PHPUnit\Framework\AssertionFailedError;

if (!function_exists('assertThrows')) {
    /**
     * Assert that an exception of the given type is thrown.
     *
     * @param string   $expectedType The expected type.
     * @param callable $act          The value to check.
     * @param string   $message      Optional message to display on failure.
     *
     * @return void
     *
     * @throws AssertionFailedError if the callable does not throw an exception of the expected type.
     */
    function assertThrows(string $expectedType, callable $act, string $message = ''): void
    {
        $exception = null;

        try {
            call_user_func($act);
        } catch (\Throwable $e) {
            $exception = $e;
        }

        call_user_func_array(
            ['PHPUnit\Framework\Assert', 'assertInstanceOf'],
            [$expectedType, $exception, $message]
        );
    }
}
