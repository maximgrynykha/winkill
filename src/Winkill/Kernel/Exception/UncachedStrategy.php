<?php

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;

final class UncachedStrategy extends \OutOfBoundsException implements Exception
{
    /**
     * @param string $strategy 
     * @param string $message 
     * @param int $code 
     * @param \Throwable|null $previous 
     * 
     * @return void 
     */
    public function __construct(
        string $strategy,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    )
    {
        $message = $message ?: 'Strategy ['.basename($strategy).'] is uncached.';
        parent::__construct($message, $code, $previous);
    }
}