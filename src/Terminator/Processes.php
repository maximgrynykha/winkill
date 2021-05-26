<?php

namespace Terminator;

use Stringy\Stringy;
use Terminator\Kernel\Enums\Attributes;
use Terminator\Kernel\Process;

final class Processes
{
    /**
     * @var array<int, \Terminator\Kernel\Process>
     */
    private array $processes;

    /**
     * @var array<int, \Terminator\Kernel\Process>
     */
    private array $whereable;

    public function __construct()
    {
        $this->processes = $this->scan();
        $this->whereable = [];
    }

    /**
     * @return array<int, \Terminator\Kernel\Process>
     */
    public function get(): array
    {
        return $this->whereable ?: $this->processes;
    }

    /**
     * @return array<int, \Terminator\Kernel\Process>
     */
    public function scan(): array
    {
        $processes = trim((string) shell_exec('tasklist'));

        return $this->parse($processes);
    }

    /**
     * @return array<int, \Terminator\Kernel\Process>
     */
    public function rescan(): array
    {
        $this->processes = $this->scan();

        return $this->processes;
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        foreach ($this->whereable as $process) {
            $process->terminate();
        }
    }

    /**
     * @param \Terminator\Kernel\Enums\Attributes $attr_name
     * @param int|string $attr_value
     * @param int|string|null $optional
     *
     * @return self
     */
    public function where(Attributes $attr_name, $attr_value, $optional = null): self
    {
        $this->whereable = [];

        foreach ($this->processes as $process) {
            if (! $process->{$attr_name}) {
                throw new \InvalidArgumentException("
                    Unsupported process attribute \"{$attr_name}\" passed.
                ");
            }

            switch ($attr_name) {
                case $attr_name->value === "process_name"
                     && mb_strpos((string) $attr_value, ".") === false:
                    $process_name = mb_strtolower($process->{$attr_name});
                    $attr_value = mb_strtolower((string) $attr_value);

                    if (in_array($attr_value, explode(".", $process_name))) {
                        $this->whereable[] = $process;
                    }
                    break;

                case $attr_name->value === "consumed_memory" && ! is_null($optional):
                    $this->factorizeOptionalParam($attr_name->value, $attr_value, $optional);
                    break;

                case $attr_name->value === "session_name":
                    $session_name = $process->{$attr_name};
                    $attr_value = (string) $attr_value;

                    if (mb_strtolower($session_name) === mb_strtolower($attr_value)) {
                        $this->whereable[] = $process;
                    }
                    break;

                default:
                    if ($process->{$attr_name} === $attr_value) {
                        $this->whereable[] = $process;
                    }
            }
        }

        return $this;
    }

    /**
     * @param string $attr_name
     * @param int|string $attr_value
     * @param int|string $optional
     *
     * @return void
     */
    private function factorizeOptionalParam(string $attr_name, $attr_value, $optional): void
    {
        switch ($optional) {
            case ">":
                $this->whereable = array_filter(
                    $this->processes,
                    fn (Process $process) => $process->{$attr_name} > $attr_value
                );
                break;
            case ">=":
                $this->whereable = array_filter(
                    $this->processes,
                    fn (Process $process) => $process->{$attr_name} >= $attr_value
                );
                break;
            case "<":
                $this->whereable = array_filter(
                    $this->processes,
                    fn (Process $process) => $process->{$attr_name} < $attr_value
                );
                break;
            case "<=":
                $this->whereable = array_filter(
                    $this->processes,
                    fn (Process $process) => $process->{$attr_name} <= $attr_value
                );
                break;
            case "=":
                $this->whereable = array_filter(
                    $this->processes,
                    fn (Process $process) => $process->{$attr_name} === $attr_value
                );
                break;
            case "!=":
                $this->whereable = array_filter(
                    $this->processes,
                    fn (Process $process) => $process->{$attr_name} !== $attr_value
                );
                break;
        }
    }

    /**
     * @param string $processes
     *
     * @return array<int, \Terminator\Kernel\Process>
     */
    private function parse(string $processes): array
    {
        $processes = explode("\r\n", $processes)[0];

        $stringy = Stringy::create($processes);

        $processes = mb_substr($processes, (int) $stringy->indexOfLast('='));
        $processes = array_slice(explode("\n", trim($processes, "=")), 1);

        return array_map(function ($process_string) {
            return new Process($process_string);
        }, $processes);
    }
}
