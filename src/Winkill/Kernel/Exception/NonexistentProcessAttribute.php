<?php

declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;

final class NonexistentProcessAttribute extends \InvalidArgumentException implements Exception
{
    /**
     * @param string $attribute
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $attribute,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = $message ?: "The attribute: [$attribute] doesn't exist
                                in processes on [" . PHP_OS_FAMILY . "] OS.";
        parent::__construct($message, $code, $previous);
    }
}
