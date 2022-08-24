<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022
 * @author     Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      0.2.0
 */

declare(strict_types=1);

namespace Codefy\Domain\EventSourcing;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\AggregateNotFoundException;

/**
 * Event store for publishing a domain event
 * and retrieving an aggregate's history.
 */
interface TransactionalEventStore
{
    /**
     * Append a domain event to the event store.
     *
     * @param DomainEvent $event
     * @param TransactionId $transactionId
     * @return void
     */
    public function append(DomainEvent $event, TransactionId $transactionId): void;

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
