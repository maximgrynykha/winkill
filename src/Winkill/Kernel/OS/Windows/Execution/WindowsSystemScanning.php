<?php declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows\Execution;

use Winkill\Kernel\Exception\SystemScanningFailure;
use Winkill\Kernel\Interface\SystemScanning;

final class WindowsSystemScanning implements SystemScanning
{
    private const COMMAND = 'tasklist';

    /**
     * @return string[]|array<int, string>
     *
     * @throws SystemScanningFailure
     */
    public function scan(): array
    {
        $processes = utf8_encode(trim((string)shell_exec(self::COMMAND)))
            ?: throw new SystemScanningFailure();

        $processes = preg_split('/\n|\r\n/', $processes);
        return array_splice($processes, 2);
    }
}
