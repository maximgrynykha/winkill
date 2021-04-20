<?php

namespace Terminator\Kernel\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self TERMINATE_BY_ID()
 * @method static self TERMINATE_BY_NAME()
 */
class Commands extends Enum
{
    /**
     * @return array<string, string>
     */
    protected static function values(): array
    {
        return [
            'TERMINATE_BY_ID' => 'taskkill /F /PID <attribute>',
            'TERMINATE_BY_NAME' => 'taskkill /IM "<attribute>" /F'
        ];
    }
}
