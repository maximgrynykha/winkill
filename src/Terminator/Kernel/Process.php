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

    /**
     * @param array<string, int|string> $process_attributes
     */
    public function __construct(array $process_attributes) 
    {
        foreach ($process_attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }
    }

    /**
     * @return string|null
     */
    public function terminate(): ?string
    {
        $command = self::prepare(
            Commands::TERMINATE_BY_ID(), 
            $this->process_id
        );        
        
        return shell_exec($command);
    }

    /**
     * @param \Terminator\Kernel\Enums\Commands $command
     * @param int|string $attribute
     * 
     * @return string
     */
    private static function prepare(Commands $command, $attribute): string
    {
        $command = str_replace("<attribute>", trim((string) $attribute), $command);

        return (is_array($command)) ? (string) $command[0] : (string) $command;
    }
}
