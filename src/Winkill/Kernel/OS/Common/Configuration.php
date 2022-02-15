<?php

namespace Winkill\Kernel\OS\Common;

use Winkill\Kernel\Exception\UncachedStrategy;
use Winkill\Kernel\Interface\{
    ProcessParsing,
    ProcessTermination,
    SystemScanning
};

abstract class Configuration
{
    /**
     * @var array
     */
    protected array $cache = [];

    /**
     * @return SystemScanning
     * 
     * @throws UncachedStrategy
    */
    protected function getCachedScanningStrategy(): SystemScanning
    {
        return $this->cache[SystemScanning::class] 
                ?? throw new UncachedStrategy(SystemScanning::class);
    }

    /**
     * @return ProcessParsing
     * 
     * @throws UncachedStrategy
    */
    protected function getCachedParsingStrategy(): ProcessParsing
    {
        return $this->cache[ProcessParsing::class] 
                ?? throw new UncachedStrategy(ProcessParsing::class);
    }

    /**
     * @return ProcessTermination
     * 
     * @throws UncachedStrategy
    */
    protected function getCachedTerminationStrategy(): ProcessTermination
    {
        return $this->cache[ProcessTermination::class] 
                ?? throw new UncachedStrategy(ProcessTermination::class);
    }
}