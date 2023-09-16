<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Domain\Aggregate\RecordsEvents;
use Codefy\Domain\EventSourcing\CorruptEventStreamException;
use Codefy\Domain\EventSourcing\EventStore;
use Codefy\Domain\EventSourcing\Projection;
use Codefy\Traits\IdentityMapAware;

use function iterator_to_array;

final class PostRepository implements AggregateRepository
{
    use IdentityMapAware;

    public function __construct(
        public readonly EventStore $eventStore,
        public readonly Projection $projection,
    ) {
    }

    /**
     * {@inheritDoc}
     * @throws CorruptEventStreamException
     */
    public function loadAggregateRoot(AggregateId $aggregateId): RecordsEvents|null
    {
        $this->retrieveFromIdentityMap($aggregateId);

        $aggregateHistory = $this->eventStore->getAggregateHistoryFor(aggregateId: $aggregateId);
        $eventSourcedAggregate = Post::reconstituteFromEventStream(
            aggregateHistory: $aggregateHistory
        );

        $this->attachToIdentityMap($eventSourcedAggregate);

        return $eventSourcedAggregate;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAggregateRoot(RecordsEvents $aggregate): void
    {
        $this->attachToIdentityMap($aggregate);

        $events = iterator_to_array($aggregate->getRecordedEvents());

        $transaction = $this->eventStore->commit(...$events);

        $committedEvents = $transaction->committedEvents();

        $this->projection->project(...$committedEvents);

        $aggregate->clearRecordedEvents();

        $this->removeFromIdentityMap($aggregate);
    }
}
