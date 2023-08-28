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

use Codefy\Domain\EventSourcing\EventStream;

interface IsEventSourced
{
    /**
     * Reconstitutes an Aggregate instance from its history of domain events.
     */
    public static function reconstituteFromEventStream(EventStream $aggregateHistory): RecordsEvents;
}
