<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\Domain\Aggregate;

interface EventSourcing
{
    /**
     * Reconstitutes an Aggregate instance from its history of domain events.
     */
    public static function reconstituteFromEventStream(EventStream $aggregateHistory): RecordsEvents;
}
