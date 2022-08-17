<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\Domain\Aggregate\AggregateId;
use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Domain\Aggregate\EventSourcedAggregate;
use Codefy\Domain\Aggregate\EventSourcedAggregateRepository;
use Codefy\Domain\Aggregate\MultipleInstancesOfAggregateDetectedException;
use Codefy\Domain\Aggregate\RecordsEvents;
use Codefy\Domain\EventSourcing\EventStore;
use Codefy\Domain\EventSourcing\Projection;
use Codefy\EventBus\EventBus;
use Codefy\Traits\IdentityMapAware;

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
        $events = $aggregate->getRecordedEvents();

        $this->attachToIdentityMap($aggregate);

        foreach ($events as $event) {
            $this->eventStore->append(event: $event);
        }
        $this->projection->project($events);

        $aggregate->clearRecordedEvents();

        $this->removeFromIdentityMap($aggregate);
    }
}
