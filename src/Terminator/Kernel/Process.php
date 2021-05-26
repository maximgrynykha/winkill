<?php

namespace Terminator\Kernel;

use Terminator\Kernel\Enums\Commands;

final class Process
{
    public string $process_name;
    public int $process_id;
    public string $session_name;
    public int $session_number;
    public int $consumed_memory;

    public function __construct(string $process)
    {
        $this->parse($process);
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $command = $this->prepare(
            Commands::TERMINATE_BY_ID(),
            $this->process_id
        );

        shell_exec($command);
    }

    /**
     * @param string $process
     *
     * @return self
     */
    private function parse(string $process): self
    {
        if (mb_strpos($process, "Services") !== false) {
            $this->session_name = "Services";
        } elseif (mb_strpos($process, "Console") !== false) {
            $this->session_name = "Console";
        }

        $process_string_parts = explode(" $this->session_name ", $process);

        $process_name_with_id = $process_string_parts[0];
        $process_session_number_with_consumed_memory = trim($process_string_parts[1]);

        $this->session_number = (int) mb_substr(
            $process_session_number_with_consumed_memory,
            0,
            (int) mb_strpos($process_session_number_with_consumed_memory, " ")
        );

        $consumed_memory = trim(mb_substr(
            $process_session_number_with_consumed_memory,
            mb_strlen((string) $this->session_number)
        ));

        $consumed_memory = mb_substr(
            $consumed_memory,
            0,
            (int) mb_strpos($consumed_memory, " ")
        );

        $this->consumed_memory = (int) filter_var(
            $consumed_memory,
            FILTER_SANITIZE_NUMBER_INT
        );

        $this->process_id = (int) mb_substr(
            $process_name_with_id,
            (int) mb_strrpos($process_name_with_id, " ") + 1
        );

        $this->process_name = (string) trim(
            mb_substr(
                $process_name_with_id,
                0,
                (int) mb_strrpos($process_name_with_id, " ")
            )
        );

        return $this;
    }

    /**
     * @param \Terminator\Kernel\Enums\Commands $command
     * @param int|string $attribute
     *
     * @return string
     */
    private function prepare(Commands $command, $attribute): string
    {
        $command = str_replace("<attribute>", trim((string) $attribute), $command);

        return (is_array($command)) ? (string) $command[0] : (string) $command;
    }
}
