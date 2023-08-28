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

use Codefy\Domain\EventSourcing\CorruptEventStreamException;
use Codefy\Domain\EventSourcing\EventStore;
use Codefy\Domain\EventSourcing\Projection;
use Codefy\Traits\IdentityMapAware;

use function iterator_to_array;

class EventSourcedAggregateRepository implements AggregateRepository
{
    use IdentityMapAware;

    public function __construct(
        protected EventStore $eventStore,
        protected Projection $projection,
    ) {
    }

    /** {@inheritDoc}
     * @throws CorruptEventStreamException
     */
    public function loadAggregateRoot(AggregateId $aggregateId): RecordsEvents
    {
        $this->retrieveFromIdentityMap($aggregateId);

        $aggregateRootClassName = $aggregateId->aggregateClassName();

        $aggregateHistory = $this->eventStore->getAggregateHistoryFor(aggregateId: $aggregateId);
        $eventSourcedAggregate = $aggregateRootClassName::reconstituteFromEventStream(
            aggregateHistory: $aggregateHistory
        );

        $this->attachToIdentityMap($eventSourcedAggregate);

        return $eventSourcedAggregate;
    }

    /** {@inheritDoc} */
    public function saveAggregateRoot(RecordsEvents $aggregate): void
    {
        $events = iterator_to_array($aggregate->getRecordedEvents());

        $transaction = $this->eventStore->commit(...$events);

        $aggregate->clearRecordedEvents();

        $committedEvents = $transaction->committedEvents();

        $this->projection->project(...$committedEvents);

        $this->removeFromIdentityMap($aggregate);
    }
}
