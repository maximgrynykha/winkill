<?php

declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;

final class UnsupportedAttributeComparisonOperator extends \InvalidArgumentException implements Exception
{
    /**
     * @param string $attribute
     * @param string $compareAs
     *
     * @return self
     */
    public static function from(string $attribute, string $compareAs): self
    {
        return new self("Unsupported comparison: $compareAs, for process attribute: $attribute.");
    }
}
