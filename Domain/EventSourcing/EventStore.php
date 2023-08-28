<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022 Joshua Parker <joshua@joshuaparker.dev>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\Domain\EventSourcing;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\AggregateNotFoundException;

/**
 * Event store for publishing a domain event
 * and retrieving an aggregate's history.
 */
interface EventStore
{
    /**
     * Append a domain event to the event store.
     *
     * @param DomainEvent $event
     * @return void
     */
    public function append(DomainEvent $event): void;

    /**
     * Appends a list of domain events to the event store.
     *
     * @param DomainEvent ...$events
     * @return Transactional
     */
    public function commit(DomainEvent ...$events): Transactional;

    /**
     * Retrieve aggregate's history based on aggregate id.
     *
     * @param AggregateId $aggregateId
     * @return EventStream
     * @throws AggregateNotFoundException|CorruptEventStreamException
     */
    public function getAggregateHistoryFor(AggregateId $aggregateId): EventStream;

    /**
     * @param AggregateId $aggregateId
     * @param int $playhead
     * @throws AggregateNotFoundException|CorruptEventStreamException
     */
    public function loadFromPlayhead(AggregateId $aggregateId, int $playhead): EventStream;
}
