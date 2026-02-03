<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022
 * @author     Joshua Parker <joshua@joshuaparker.dev>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\Domain\Aggregate;

use Closure;

use function array_map;
use function end;
use function explode;
use function implode;
use function preg_split;

use const PREG_SPLIT_NO_EMPTY;

final class AggregateType
{
    /**
     * Convert class name to a delimited string.
     *
     * @param class-string $className
     * @param callable|null $callback
     * @param string $delimiter
     * @return string
     */
    public static function fromClassName(
        string $className,
        ?callable $callback = null,
        string $delimiter = '-'
    ): string {
        $callback = $callback instanceof Closure
        ? $callback
        : static fn (string $value): string => strtolower($value);

        // Remove namespace
        $parts = explode('\\', $className);
        $classNameWithoutNamespace = end($parts);

        // Split on capitals
        $segments = preg_split(
            '/(?=[A-Z])/',
            $classNameWithoutNamespace,
            -1,
            PREG_SPLIT_NO_EMPTY
        ) ?: [];

        // Transform + implode
        return implode(
            $delimiter,
            array_map($callback, $segments)
        );
    }
}
