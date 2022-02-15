<?php declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows\Execution\Termination;

use Winkill\Kernel\Exception\ProcessTerminationFailure;
use Winkill\Kernel\Interface\{Process, ProcessTermination};

final class WindowsProcessTerminationById implements ProcessTermination
{
    private const COMMAND = 'taskkill /F /PID <attribute>';

    /**
     * @param Process $process
     *
     * @return void
     *
     * @throws ProcessTerminationFailure
     */
    public function terminate(Process $process): void
    {
        exec((string)str_replace(
            search: '<attribute>',
            replace: (string)$process->process_id,
            subject: self::COMMAND
        )) ?: throw new ProcessTerminationFailure($process);
    }
}
