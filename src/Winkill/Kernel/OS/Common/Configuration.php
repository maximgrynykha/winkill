<?php

namespace Winkill\Kernel\OS\Common;

use Winkill\Kernel\Interface\{
    ProcessParsing,
    ProcessTermination,
    SystemScanning
};
use Winkill\Kernel\Interface\Configuration as ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected array $cache = [];

    public function __construct(protected readonly ConfigurationInterface $configuration)
    {
    }

    /**
     * @return SystemScanning
     */
    public function createScanningStrategy(): SystemScanning
    {
        return $this->cache[SystemScanning::class] ??= $this->configuration->createScanningStrategy();
    }

    /**
     * @return ProcessParsing
     */
    public function createParsingStrategy(): ProcessParsing
    {
        return $this->cache[ProcessParsing::class] ??= $this->configuration->createParsingStrategy();
    }

    /**
     * @return ProcessTermination
     */
    public function createTerminationStrategy(): ProcessTermination
    {
        return $this->cache[ProcessTermination::class] ??= $this->configuration->createTerminationStrategy();
    }
}
