<?php declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows\Execution\Killing;

use Winkill\Kernel\Exception\ProcessKillingFailure;
use Winkill\Kernel\Interface\Process;
use Winkill\Kernel\Interface\ProcessKilling;

final class WindowsProcessKillingById implements ProcessKilling
{
    private const COMMAND = 'taskkill /F /PID <attribute>';

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
            replace: (string)$process->process_id,
            subject: self::COMMAND
        )) ?: throw new ProcessKillingFailure($process);
    }
}
