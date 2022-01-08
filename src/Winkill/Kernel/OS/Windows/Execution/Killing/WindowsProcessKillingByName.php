<?php

declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows\Execution\Killing;

use Winkill\Kernel\Exception\ProcessKillingFailure;
use Winkill\Kernel\Interface\Process;
use Winkill\Kernel\Interface\ProcessKilling;

final class WindowsProcessKillingByName implements ProcessKilling
{
    private const COMMAND = 'taskkill /IM "<attribute>" /F';

    /**
     * @param Process $process
     *
     * @return void
     *
     * @throws ProcessKillingFailure
     */
    public function kill(Process $process): void
    {
        exec((string)str_replace(
            search: '<attribute>',
            replace: $process->process_name,
            subject: self::COMMAND
        )) ?: throw new ProcessKillingFailure($process);
    }
}
