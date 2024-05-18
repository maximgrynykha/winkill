<?php

declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;

final class UnsupportedAttributeValue extends \InvalidArgumentException implements Exception
{
    /**
     * @param string $attribute
     * @param int|string $value
     *
     * @return self
     */
    public static function from(string $attribute, int|string $value): self
    {
        return new self("Unsupported value: $value, for process attribute: $attribute.");
    }
}
