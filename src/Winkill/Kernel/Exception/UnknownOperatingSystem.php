<?php

declare(strict_types=1);

namespace Winkill\Kernel\Exception;

use Winkill\Kernel\Interface\Exception;

final class UnknownOperatingSystem extends \UnexpectedValueException implements Exception
{
    /**
     * @var string
     */
    protected $message = 'Cannot make configuration for an unknown operating system.';
}
