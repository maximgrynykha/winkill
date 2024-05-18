<?php

declare(strict_types=1);

namespace Winkill;

use Winkill\Kernel\Exception\{
    SystemScanningFailure,
    UnknownOperatingSystem,
    UnsupportedOperatingSystem
};
use Winkill\Kernel\Interface\Configuration as ConfigurationInterface;
use Winkill\Kernel\OS\Common\Configuration as CachingConfiguration;
use Winkill\Kernel\OS\Windows\WindowsConfiguration;
use Winkill\Kernel\Processes;

final class Winkill
{
    /**
     * @param ConfigurationInterface|null $factory
     */
    public function __construct(
        private readonly ?ConfigurationInterface $factory = null,
    ) {
    }

    /**
     * Composition Root
     *
     * Look at the current operating system (OS) scan all
     * active processes appropriately to it. And return
     * processes' builder to manipulate over them.
     *
     * ```
     * $terminator = new Winkill();
     * $processes = $terminator->scan();
     * ```
     * @see https://blog.ploeh.dk/2011/07/28/CompositionRoot/
     *
     * @return Processes
     *
     * @throws SystemScanningFailure
     * @throws UnknownOperatingSystem
     * @throws UnsupportedOperatingSystem
     */
    public function scan(): Processes
    {
        $factory = match (PHP_OS_FAMILY) {
            'Windows' => new WindowsConfiguration(),
            'Unknown' => throw new UnknownOperatingSystem(),
            default => throw new UnsupportedOperatingSystem()
        };

        return new Processes(
            new CachingConfiguration($this->factory ?: $factory)
        );
    }
}
