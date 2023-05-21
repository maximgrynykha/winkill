<?php

declare(strict_types=1);

namespace Winkill\Kernel\Interface;

/**
 * Abstract Factory
 *
 * @see https://refactoring.guru/design-patterns/abstract-factory
 */
interface Configuration
{
    /**
     * @return SystemScanning
     */
    public function createScanningStrategy(): SystemScanning;

    /**
     * @return ProcessParsing
     */
    public function createParsingStrategy(): ProcessParsing;

    /**
     * @return ProcessTermination
     */
    public function createTerminationStrategy(): ProcessTermination;
}
