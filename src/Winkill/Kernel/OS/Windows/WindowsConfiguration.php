<?php declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows;

use Winkill\Kernel\Interface\Configuration;
use Winkill\Kernel\Interface\ProcessKilling;
use Winkill\Kernel\Interface\ProcessParsing;
use Winkill\Kernel\Interface\SystemScanning;
use Winkill\Kernel\OS\Windows\Execution\{
    Killing\WindowsProcessKillingById,
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
     * @return ProcessKilling
     */
    public function createTerminationStrategy(): ProcessKilling
    {
        return new WindowsProcessKillingById();
    }
}
