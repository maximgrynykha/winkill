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
    public function update(): array
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
                    if (in_array($attr_value, explode(".", $process->process_name))) {
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
     * @return array<int, \Terminator\Kernel\Process>
     */
    private function scan(): array
    {
        $processes = trim((string) shell_exec('tasklist'));

        return $this->parse($processes);
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

        $processes = trim(mb_substr($processes, (int) $stringy->indexOfLast('=')), "=");
        $processes = array_slice(explode("\n", $processes), 1);

        $pretty_processes = [];

        foreach ($processes as $process) {
            $process_name = (string) null;
            $process_id = (int) null;
            $session_name = (string) null;
            $session_number = (int) null;
            $consumed_memory = (int) null;

            if (mb_strpos($process, "Services") !== false) {
                $session_name = "Services";
            } elseif (mb_strpos($process, "Console") !== false) {
                $session_name = "Console";
            }

            $process_string_parts = explode(" $session_name ", $process);

            $process_name_with_id = $process_string_parts[0];
            $process_session_number_with_consumed_memory = trim($process_string_parts[1]);

            $session_number = (int) mb_substr(
                $process_session_number_with_consumed_memory,
                0,
                (int) mb_strpos($process_session_number_with_consumed_memory, " ")
            );

            $consumed_memory = trim(mb_substr(
                $process_session_number_with_consumed_memory,
                mb_strlen((string) $session_number)
            ));

            $consumed_memory = mb_substr(
                $consumed_memory,
                0,
                (int) mb_strpos($consumed_memory, " ")
            );

            $consumed_memory = (int) filter_var($consumed_memory, FILTER_SANITIZE_NUMBER_INT);

            $process_id = (int) mb_substr(
                $process_name_with_id,
                (int) mb_strrpos($process_name_with_id, " ") + 1
            );

            $process_name = (string) trim(
                mb_substr(
                    $process_name_with_id,
                    0,
                    (int) mb_strrpos($process_name_with_id, " ")
                )
            );

            $pretty_processes[] = new Process(compact(
                "process_name",
                "process_id",
                "session_name",
                "session_number",
                "consumed_memory"
            ));
        }

        return $pretty_processes;
    }

    /*
        getActiveProcesses
        getActiveProcessById
        getActiveProcessesByName
        getActiveProcessesByExtension
        getActiveProcessNameById
        getActiveProcessesIdsByName
        getActiveProcessesIdsByExtension
        getActiveProcessesNamesByExtension
        getActiveProcessByConsumedMemory
    */

    // terminate one task by process id
    //* terminate one task by process name

    // terminate all task by process ids
    // terminate all task by process name

    // terminate all task by process extention
}
