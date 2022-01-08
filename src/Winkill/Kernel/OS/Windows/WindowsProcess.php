<?php declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows;

use Winkill\Kernel\Exception\NonexistentProcessAttribute;
use Winkill\Kernel\Interface\Process;
use Winkill\Kernel\OS\Common\Comparison;

final class WindowsProcess implements Process, \Stringable
{
    /**
     * @param string $process_name
     * @param int $process_id
     * @param string $session_name
     * @param int $session_number
     * @param int $consumed_memory
     */
    public function __construct(
        public readonly string $process_name,
        public readonly int $process_id,
        public readonly string $session_name,
        public readonly int $session_number,
        public readonly int $consumed_memory
    ) {}

    /**
     * @param string $attribute
     * @param string $compareAs
     * @param int|string $value
     *
     * @return bool
     */
    public function handleAttribute(
        string     $attribute,
        string     $compareAs,
        int|string $value
    ): bool
    {
        return match (trim($attribute)) {
            'process_name' => $this->handleProcessName(mb_strtolower(trim((string)$value))),
            'process_id' => $this->process_id === $value,
            'session_name' => $this->session_name === mb_strtolower(trim((string)$value)),
            'session_number' => $this->session_number === $value,
            'consumed_memory' => $this->handleConsumedMemory($value, trim($compareAs)),
            default => throw new NonexistentProcessAttribute($attribute)
        };
    }

    /**
     * @param string $process_name
     *
     * @return bool
     */
    private function handleProcessName(string $process_name): bool
    {
        $is_handled = $process_name === mb_strtolower($this->process_name);

        if (str_contains($process_name, '.')
            || str_contains($this->process_name, '.')
        ) {
            $accepted_process_name_segments = explode(
                separator: '.',
                string: $process_name
            );

            $internal_process_name_segments = explode(
                separator: '.',
                string: mb_strtolower($this->process_name)
            );

            $is_handled = empty(array_diff(
                $accepted_process_name_segments,
                $internal_process_name_segments
            ));
        }

        return $is_handled;
    }

    /**
     * @param int|string $consumed_memory
     * @param string $compareAs
     *
     * @return bool
     */
    private function handleConsumedMemory(
        int|string $consumed_memory,
        string     $compareAs
    ): bool
    {
        return (is_int($consumed_memory) && Comparison::tryFrom($compareAs)
                ->compare($this->consumed_memory, $consumed_memory));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}
