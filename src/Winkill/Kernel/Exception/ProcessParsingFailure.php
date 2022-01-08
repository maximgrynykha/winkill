<?php

declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;

final class ProcessParsingFailure extends \LogicException implements Exception
{
    /**
     * @param string $process
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        private string $process,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        if (!$message) {
            $message = "The process cannot be parsed." . PHP_EOL . $this->process;
        }

        parent::__construct($message, $code, $previous);
    }
}
