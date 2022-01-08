<?php declare(strict_types=1);

namespace Winkill\Kernel;

use Winkill\Kernel\Exception\ProcessKillingFailure;
use Winkill\Kernel\Exception\ProcessParsingFailure;
use Winkill\Kernel\Exception\SystemScanningFailure;
use Winkill\Kernel\Exception\UnsupportedCompareOperator;
use Winkill\Kernel\Interface\Configuration;
use Winkill\Kernel\Interface\Process;
use Winkill\Kernel\Interface\ProcessKilling;
use Winkill\Kernel\OS\Common\Comparison;

/**
 * Builder Pattern + Facade Pattern*
 *
 * @see https://refactoring.guru/design-patterns/builder
 */
final class Processes
{
    /**
     * Objects Collection
     *
     * @var Process[]|array<int, Process>
     */
    private array $scanned;

    /**
     * Objects Collection
     *
     * @var Process[]|array<int, Process>
     */
    private array $selected;

    /**
     * @param Configuration $factory
     *
     * @throws SystemScanningFailure
     */
    public function __construct(
        private readonly Configuration $factory,
    )
    {
        $this->scanned = $this->scan();
        $this->selected = [];
    }

    /**
     * Return array of selected or scanned processes
     *
     * Usage:
     *
     * ```
     * $winkill = new Winkill();
     * $processes = $winkill->scan();
     *
     * $scanned = $processes->get();
     *
     * // Or by specifying selection
     *
     * $selected = $processes->where(
     *      attribute: 'consumed_memory',
     *      compareAs: '=',
     *      value: 500
     * )->get();
     * ```
     *
     * @return Process[]|array<int, Process>
     */
    public function get(): array
    {
        return $this->selected ?: $this->scanned;
    }

    /**
     * Select from scanned processes
     * appropriate ones to conditions
     *
     * Usage:
     *
     * ```
     * $winkill = new Winkill();
     * $processes = $winkill->scan();
     *
     * $processes = $processes->where(
     *      attribute: 'process_name',
     *      compareAs: '=',
     *      value: 'chrome' # You can pass the process name in the following formats:
     *                      # [string]: simple name    (chrome)
     *                      # [string]: name with .ext (chrome.exe)
     *                      # [string]: uppercase name (Chrome.exe)
     * );
     * ```
     *
     * - List of all available attributes — {@see \Winkill\Kernel\OS\Windows\WindowsProcess}
     * - List of all available compare operators — {@see \Winkill\Kernel\OS\Common\Comparison}
     *
     * @param string $attribute
     * @param string $compareAs
     * @param int|string $value
     *
     * @return $this
     *
     * @throws UnsupportedCompareOperator
     */
    public function where(
        string     $attribute,
        string     $compareAs,
        int|string $value
    ): self
    {
        if (!in_array(trim($compareAs), Comparison::values())) {
            throw new UnsupportedCompareOperator();
        }

        foreach ($this->scanned as $process) {
            $is_handled = $process->handleAttribute(
                $attribute, $compareAs, $value
            );

            if ($is_handled) $this->selected[] = $process;
        }

        return $this;
    }

    /**
     * Kill (terminate) the selected processes
     * (optionally, by using custom strategy)
     *
     * Usage:
     *
     * ```
     * $winkill = new Winkill();
     * $processes = $winkill->scan();
     *
     * $killed = $processes->where(
     *      attribute: 'consumed_memory',
     *      compareAs: '=',
     *      value: 500
     * )->kill();
     * ```
     *
     * You can create your own process killing strategy by implementing
     * kernel interface {@see \Winkill\Kernel\Interface\ProcessKilling}
     *
     * The custom strategy will execute for each of selected processes
     * (from processes selected by using $processes->where(...)).
     *
     * ```
     * $winkill = new Winkill();
     * $processes = $winkill->scan();
     *
     * $killed = $processes->where(
     *      attribute: 'consumed_memory',
     *      compareAs: '=',
     *      value: 500
     * )->kill(strategy: new CustomKilling());
     * ```
     *
     * @param ProcessKilling|null $strategy
     *
     * @return Process[]|array<int, Process> Those processes from
     *                                       the selected which were killed.
     *
     * @throws ProcessKillingFailure
     */
    public function kill(?ProcessKilling $strategy = null): array
    {
        /** @var Process[]|array<int, Process> $killed */
        $killed = [];

        $strategy = $strategy ?: $this->factory->createTerminationStrategy();

        foreach ($this->selected as $process) {
            $strategy->kill($process);
            $killed[] = $process;
        }

        return $killed;
    }

    /**
     * Return array of process instances
     * by parsing the OS-scanning output
     *
     * @return Process[]|array<int, Process>
     *
     * @throws SystemScanningFailure
     * @throws ProcessParsingFailure
     */
    private function scan(): array
    {
        $parser = $this->factory->createParsingStrategy();

        return array_map(
            static fn(string $process): Process => $parser->parse($process),
            $this->factory->createScanningStrategy()->scan()
        );
    }
}
