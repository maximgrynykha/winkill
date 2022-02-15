<?php declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\{Exception, Process};

final class ProcessTerminationFailure extends \LogicException implements Exception
{
    /**
     * @param Process $process
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        Process $process,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = $message ?: 'The process cannot be killed.' . PHP_EOL . $process;
        parent::__construct($message, $code, $previous);
    }
}
