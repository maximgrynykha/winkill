<?php

declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows;

use Winkill\Kernel\Exception\UnsupportedAttributeComparisonOperator;
use Winkill\Kernel\Exception\NonexistentProcessAttribute;
use Winkill\Kernel\Exception\UnsupportedAttributeValue;
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
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
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
        $attribute = mb_strtolower(trim($attribute));
        $compareAs = mb_strtolower(trim($compareAs));
        $value = mb_strtolower(trim((string)$value));

        switch ($attribute) {
            case 'process_name':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }

                // chrome.exe
                if (preg_match('/^\w+(\.\w+)+$/', $value)) {
                    return $c->compare($this->process_name, $value); # by_name+by_ext (full match)
                }
                // chrome
                if (preg_match('/^[^.]*$/', $value)) {
                    return $c->compare(mb_strstr($this->process_name, '.', true), $value); # by_name
                }
                // .exe
                if (preg_match('/^\./', $value)) {
                    return $c->compare(mb_strstr($this->process_name, '.', false), $value); # by_ext
                }

                return $c->compare($this->process_name, $value);
            case 'process_id':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }

                return $c->compare($this->process_id, (int)$value);
            case 'session_name':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }
                if (!in_array($value, ['console', 'services'])) {
                    throw UnsupportedAttributeValue::from($attribute, $value);
                }

                return $c->compare($this->session_name, $value);
            case 'session_number':
                if (!in_array($c = Comparison::tryFrom($compareAs), [Comparison::EQUAL, Comparison::NOT_EQUAL])) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }
                if (!in_array($value, [0, 1])) {
                    throw UnsupportedAttributeValue::from($attribute, $value);
                }

                return $c->compare($this->session_number, (int)$value);
            case 'consumed_memory':
                if (!in_array($c = Comparison::tryFrom($compareAs), Comparison::values())) {
                    throw UnsupportedAttributeComparisonOperator::from($attribute, $compareAs);
                }

                return $c->compare($this->consumed_memory, (int)$value);
            default:
                throw new NonexistentProcessAttribute($attribute);
        }
    }
}
