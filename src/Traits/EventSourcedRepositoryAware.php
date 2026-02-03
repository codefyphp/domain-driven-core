<?php

declare(strict_types=1);

namespace Codefy\Traits;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\RecordsEvents;
use Codefy\Domain\EventSourcing\CorruptEventStreamException;

use function iterator_to_array;

/** @phpstan-ignore trait.unused */
trait EventSourcedRepositoryAware
{
    use IdentityMapAware;

    /**
     * {@inheritDoc}
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

        $committedEvents = $transaction->committedEvents;

        $this->projection->project(...$committedEvents);

        $this->removeFromIdentityMap($aggregate);
    }
}
