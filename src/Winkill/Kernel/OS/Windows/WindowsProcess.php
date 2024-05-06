<?php

declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows;

use Winkill\Kernel\Exception\UnsupportedAttributeComparisonOperator;
use Winkill\Kernel\Exception\NonexistentProcessAttribute;
use Winkill\Kernel\Interface\Process;
use Winkill\Kernel\OS\Common\Comparison;

final class WindowsProcess implements Process, \Stringable
{
    public readonly string $process_name;
    public readonly int $process_id;
    public readonly string $session_name;
    public readonly int $session_number;
    public readonly int $consumed_memory;

    /**
     * @param string $process_name
     * @param int $process_id
     * @param string $session_name
     * @param int $session_number
     * @param int $consumed_memory
     */
    public function __construct(
        string $process_name,
        int $process_id,
        string $session_name,
        int $session_number,
        int $consumed_memory
    ) {
        $this->process_name = mb_strtolower(trim($process_name));
        $this->session_name = mb_strtolower(trim($session_name));
    }

    /**
     * @param string $attribute
     * @param string $compareAs
     * @param int|string $value
     *
     * @return bool
     */
    public function handleAttribute(
        string $attribute,
        string $compareAs,
        int|string $value
    ): bool {
        $attribute = trim($attribute);
        $compareAs = trim($compareAs);

        switch ($attribute) {
            case 'process_name':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }
                $value = mb_strtolower(trim((string)$value));
                return $this->handleProcessName($value, $c);
            case 'process_id':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }
                return $c->compare($this->process_id, +$value);
            case 'session_name':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }
                $value = mb_strtolower(trim((string)$value));
                return $c->compare($this->session_name, $value);
            case 'session_number':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }
                return $c->compare($this->session_number, +$value);
            case 'consumed_memory':
                if (!in_array($c = Comparison::tryFrom($compareAs), Comparison::values())) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }
                return $c->compare($this->consumed_memory, +$value);
            default:
                throw new NonexistentProcessAttribute($attribute);
        }
    }

    /**
     * @param string $process_name
     * @param Comparison $comparison
     *
     * @return bool
     */
    private function handleProcessName(string $process_name, Comparison $comparison): bool
    {
        $is_handled = $comparison->compare($this->process_name, $process_name);

        if (str_contains($process_name, '.') || str_contains($this->process_name, '.')) {
            $process_name_segments_in_argument = explode('.', $process_name);
            $process_name_segments_in_instance = explode('.', $this->process_name);

            $diff = array_diff(
                $process_name_segments_in_argument,
                $process_name_segments_in_instance
            );

            $is_handled = match ($comparison) {
                Comparison::EQUAL => empty($diff),
                Comparison::NOT_EQUAL => !empty($diff),
            };
        }

        return $is_handled;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}
