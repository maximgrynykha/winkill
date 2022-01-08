<?php declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;
use Winkill\Kernel\OS\Common\Comparison;

final class UnsupportedCompareOperator extends \InvalidArgumentException implements Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string      $message = "",
        int         $code = 0,
        ?\Throwable $previous = null
    )
    {
        if (!$message) {
            $message = 'You try to use an unsupported compare operator. Use one
                        of these instead: ' . implode(', ', Comparison::values());
        }

        parent::__construct($message, $code, $previous);
    }
}
