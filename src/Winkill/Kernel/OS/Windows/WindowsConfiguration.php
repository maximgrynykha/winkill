<?php

declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows;

use Winkill\Kernel\Interface\{
    Configuration,
    ProcessTermination,
    ProcessParsing,
    SystemScanning
};
use Winkill\Kernel\OS\Windows\Execution\{
    Termination\WindowsProcessTerminationById,
    WindowsProcessParsing,
    WindowsSystemScanning
};

final class WindowsConfiguration implements Configuration
{
    /**
     * @return SystemScanning
    */
    public function createScanningStrategy(): SystemScanning
    {
        return new WindowsSystemScanning();
    }

    /**
     * @return ProcessParsing
    */
    public function createParsingStrategy(): ProcessParsing
    {
        return new WindowsProcessParsing();
    }

    /**
     * @return ProcessTermination
    */
    public function createTerminationStrategy(): ProcessTermination
    {
        return new WindowsProcessTerminationById();
    }
}
