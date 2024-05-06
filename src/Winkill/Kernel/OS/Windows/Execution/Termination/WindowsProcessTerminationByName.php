<?php

declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows\Execution\Termination;

use Winkill\Kernel\Exception\ProcessTerminationFailure;
use Winkill\Kernel\Interface\{Process, ProcessTermination};

final class WindowsProcessTerminationByName implements ProcessTermination
{
    private const COMMAND = 'taskkill /IM "<attribute>" /F';

    /**
     * @param Process $process
     *
     * @return void
     *
     * @throws ProcessTerminationFailure
     */
    public function terminate(Process $process): void
    {
        $attribute = escapeshellarg($process->process_name);

        exec((string)str_replace(
            search: '<attribute>',
            replace: $attribute,
            subject: self::COMMAND
        )) ?: throw new ProcessTerminationFailure($process);
    }
}
