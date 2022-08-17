<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.1.0
 */

declare(strict_types=1);

namespace Codefy\Domain\EventSourcing;

use Codefy\Domain\Aggregate\CorruptEventStreamException;
use Codefy\Domain\Aggregate\EventStream;
use Codefy\Domain\Aggregate\AggregateId;

/**
 * Event store for publishing a domain event
 * and retrieving an aggregate's history.
 */
interface EventStore
{
    /**
     * Append domain events to the event store.
     *
     * @param DomainEvent $event
     * @return void
     */
    public function append(DomainEvent $event): void;

    /**
     * Retrieve aggregate's history based on aggregate id.
     *
     * @param AggregateId $aggregateId
     * @return EventStream
     */
    public function getAggregateHistoryFor(AggregateId $aggregateId): EventStream;

    /**
     * @throws CorruptEventStreamException
     */
    public function loadFromPlayhead(AggregateId $aggregateId, int $playhead): EventStream;
}
