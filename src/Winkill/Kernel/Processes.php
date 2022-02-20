<?php declare(strict_types=1);

namespace Winkill\Kernel;

use Winkill\Kernel\Exception\{
    ProcessTerminationFailure,
    ProcessParsingFailure,
    SystemScanningFailure,
    UnsupportedComparisonOperator
};
use Winkill\Kernel\Interface\{
    Configuration,
    Process,
    ProcessTermination,
};
use Winkill\Kernel\OS\Common\Comparison;

/**
 * Builder Pattern + Facade Pattern*
 *
 * @see https://refactoring.guru/design-patterns/builder
 */
final class Processes
{
    /**
     * Object Collection
     *
     * @var Process[]|array<int, Process>
     */
    private array $scanned;

    /**
     * Object Collection
     *
     * @var array<string, bool|array<int, Process>>
     */
    private array $selected;

    /**
     * @param Configuration $factory
     *
     * @throws SystemScanningFailure
     * @throws ProcessParsingFailure
     */
    public function __construct(private readonly Configuration $factory)
    {
        $scanning_strategy = $this->factory->createScanningStrategy();
        $parsing_strategy = $this->factory->createParsingStrategy();
        
        foreach ($scanning_strategy->scan() as $process) {
            $this->scanned[] = $parsing_strategy->parse($process);
        }

        $this->selected['is'] = false;
        $this->selected['processes'] = [];
    }

    /**
     * Return an array of selected or scanned processes
     * 
     * (Note: this method is non-idempotent - in one 
     * case, it returns one thing, in the other case, 
     * something else.)
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
        $this->selected['is'] = ! $this->selected['is'];

        return (! $this->selected['is']) 
            ? $this->selected['processes'] 
            : $this->scanned;
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
     * @throws UnsupportedComparisonOperator
     */
    public function where(
        string     $attribute,
        string     $compareAs,
        int|string $value
    ): static
    {
        if (! in_array(trim($compareAs), Comparison::values())) {
            throw new UnsupportedComparisonOperator($compareAs);
        }

        $this->selected['processes'] = []; // Remove all previous selected processes
        $this->selected['is'] = false; // Begin processes selection

        foreach ($this->scanned as $process) {
            if ($process->handleAttribute($attribute, $compareAs, $value)) {
                $this->selected['processes'][] = $process;
            }
        }

        $this->selected['is'] = true; // Finish processes selection

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
     * $terminated = $processes->where(
     *      attribute: 'consumed_memory',
     *      compareAs: '=',
     *      value: 500
     * )->kill();
     * ```
     *
     * You can create your own process termination strategy by implementing
     * kernel interface {@see \Winkill\Kernel\Interface\ProcessTermination}
     *
     * The custom strategy will execute for each of selected processes
     * (from processes selected by using $processes->where(...)).
     *
     * ```
     * $winkill = new Winkill();
     * $processes = $winkill->scan();
     *
     * $terminated = $processes->where(
     *      attribute: 'consumed_memory',
     *      compareAs: '=',
     *      value: 500
     * )->kill(strategy: new CustomTermination());
     * ```
     *
     * @param ProcessTermination|null $strategy
     *
     * @return Process[]|array<int, Process> Those processes from
     *                                       the selected which were terminated.
     *
     * @throws ProcessTerminationFailure
     */
    public function kill(?ProcessTermination $strategy = null): array
    {
        /** @var Process[]|array<int, Process> $terminated */
        $terminated = [];

        /** @var ProcessTermination $termination_strategy */
        $termination_strategy = $strategy ?: $this->factory->createTerminationStrategy();

        foreach ($this->selected['processes'] as $process) {
            $termination_strategy->terminate($process);
            $terminated[] = $process;
        }

        return $terminated;
    }
}
