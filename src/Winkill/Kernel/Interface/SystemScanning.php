<?php

declare(strict_types=1);

namespace Winkill\Kernel\Interface;

use Winkill\Kernel\Exception\SystemScanningFailure;

/**
 * Strategy Pattern
 *
 * @see https://refactoring.guru/design-patterns/strategy
 */
interface SystemScanning
{
    /**
     * @return string[]|array<int, string> Command output with list
     *                                     of all processes running
     *                                     in the system.
     *
     * @throws SystemScanningFailure
     */
    public function scan(): array;
}
