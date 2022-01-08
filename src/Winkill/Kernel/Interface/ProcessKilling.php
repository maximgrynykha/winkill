<?php declare(strict_types=1);

namespace Winkill\Kernel\Interface;

use Winkill\Kernel\Exception\ProcessKillingFailure;

/**
 * Strategy Pattern
 *
 * @see https://refactoring.guru/design-patterns/strategy
 */
interface ProcessKilling
{
    /**
     * @param Process $process
     *
     * @return void
     *
     * @throws ProcessKillingFailure
     */
    public function kill(Process $process): void;
}
