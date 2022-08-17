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

use Codefy\Domain\EventSourcing\EventStore;
use Codefy\Domain\EventSourcing\Projection;
use Codefy\EventBus\EventBus;
use Codefy\Traits\IdentityMapAware;

use function Qubus\Support\Helpers\is_null__;
use function var_dump;

class EventSourcedAggregateRepository implements AggregateRepository
{
    use IdentityMapAware;

    public function __construct(
        protected EventStore $eventStore,
        protected Projection $projection,
    ) {
    }

    /**
     * {@inheritDoc}
     * @throws MultipleInstancesOfAggregateDetectedException
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

    /**
     * {@inheritDoc}
     */
    public function saveAggregateRoot(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();

        foreach ($events as $event) {
            $this->eventStore->append(event: $event);
        }
        $this->projection->project($events);

        $aggregate->clearRecordedEvents();

        $this->removeFromIdentityMap($aggregate);
    }
}
