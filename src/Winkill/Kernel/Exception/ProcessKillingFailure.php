<?php

declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;
use Winkill\Kernel\Interface\Process;

final class ProcessKillingFailure extends \LogicException implements Exception
{
    /**
     * @param Process $process
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        private Process $process,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        if (!$message) {
            $message = 'The process cannot be killed.' . PHP_EOL . $this->process;
        }

        parent::__construct($message, $code, $previous);
    }
}
