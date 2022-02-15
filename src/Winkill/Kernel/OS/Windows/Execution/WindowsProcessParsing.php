<?php declare(strict_types=1);

namespace Winkill\Kernel\OS\Windows\Execution;

use Winkill\Kernel\Exception\ProcessParsingFailure;
use Winkill\Kernel\Interface\{Process, ProcessParsing};
use Winkill\Kernel\OS\Windows\WindowsProcess;

final class WindowsProcessParsing implements ProcessParsing
{
    /**
     * @see https://proglib.io/p/learn-regex
     * @see https://www.youtube.com/watch?v=_pLpx6btq6U
     * @see https://w.wiki/4dir
     * @see https://ravesli.com/uroki-po-regexp/
     *
     * @see https://regex101.com/
     * @see https://stackoverflow.com/questions/7124778
     *
     * @param string $process
     *
     * @return Process
     *
     * @throws ProcessParsingFailure
     */
    public function parse(string $process): Process
    {
        preg_match(pattern: '/' .
            '^(?<process_name>.+?\S+)\s+?(?<process_id>\d+)\s+'
            . '(?<session_name>.+?\S+)\s+(?<session_number>\d)\s+'
            . '(?<consumed_memory>.+)'
            . '/iu',
            subject: trim($process), matches: $attributes
        ) ?: throw new ProcessParsingFailure($process);

        /** @var array<int|string, string> $attributes */
        foreach ($attributes as $index => &$attribute) {
            if (is_int($index)) {
                unset($attributes[$index]);
            }

            $attribute = trim($attribute);
        }

        $attributes['process_id'] = (int)$attributes['process_id'];
        $attributes['session_number'] = (int)$attributes['session_number'];

        $attributes['consumed_memory'] = (int)preg_replace(
            pattern: '/\D/',
            replacement: '',
            subject: $attributes['consumed_memory']
        );

        return new WindowsProcess(...$attributes);
    }
}
