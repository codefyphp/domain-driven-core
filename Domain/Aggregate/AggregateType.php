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

use function array_map;
use function end;
use function explode;
use function implode;
use function preg_split;
use function str_replace;

use const PREG_SPLIT_NO_EMPTY;

final class AggregateType
{
    /**
     * Convert class name to a delimited string.
     *
     * @param object|string $className
     * @param callable|string|null $callback
     * @param string $delimiter
     * @return string
     */
    public static function fromClassName(
        object|string $className,
        callable|string|null $callback = 'strtolower',
        string $delimiter = '-'
    ): string {
        // Remove namespace from class if present.
        $explode = explode('\\', $className);
        $classNameWithoutNamespace = end($explode);
        // Split the class name into parts and remove any empty array values.
        $parts = preg_split('/(?=[A-Z])/', $classNameWithoutNamespace, -1, PREG_SPLIT_NO_EMPTY);
        // Convert each array value to lowercase.
        $array = array_map($callback, $parts);
        // Convert the array into a string with spaces in between them.
        $implode = implode(' ', $array);

        // Replace the spaces with the delimiter and give back the delimited string.
        return str_replace(' ', $delimiter, $implode);
    }
}
