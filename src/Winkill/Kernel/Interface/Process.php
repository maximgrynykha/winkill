<?php declare(strict_types=1);

namespace Winkill\Kernel\Interface;

/**
 * Command Pattern
 *
 * @see https://refactoring.guru/design-patterns/command
 */
interface Process
{
    /**
     * Attribute name compare as {comparison operator} to the value.
     * Supported operators: {@see \Winkill\Kernel\OS\Common\Comparison}.
     *
     * @param string $attribute
     * @param string $compareAs
     * @param int|string $value
     *
     * @return bool
     */
    public function handleAttribute(
        string $attribute,
        string $compareAs,
        int|string $value
    ): bool;
}
