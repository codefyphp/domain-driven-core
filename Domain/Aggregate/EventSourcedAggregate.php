<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Codefy\Domain\Aggregate;

use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\DomainEvents;
use Codefy\Traits\EventProducerAware;
use Codefy\Traits\EventSourcedAware;
use Codefy\Traits\PublisherAware;
use Qubus\Exception\Data\TypeException;

abstract class EventSourcedAggregate implements AggregateRoot, EventSourcing
{
    use EventProducerAware;
    use EventSourcedAware;
    use PublisherAware;

    protected function __construct(public readonly AggregateId $aggregateId)
    {
    }

    /**
     * Records, applies, and publishes a domain event.
     */
    protected function recordApplyAndPublishThat(DomainEvent $event): void
    {
        $this->recordThat(event: $event);
        $this->applyThat(event: $event);
        $this->publishThat(event: $event);
    }

    protected function applyThat(DomainEvent $event): void
    {
        $this->when(event: $event);
    }

    /** {@inheritDoc} */
    public function hasRecordedEvents(): bool
    {
        return !empty($this->recordedEvents);
    }

    /**
     * {@inheritDoc}
     *
     */
    public function getRecordedEvents(): DomainEvents
    {
        return DomainEvents::fromArray(events: $this->recordedEvents);
    }

    /** {@inheritDoc} */
    public function clearRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }

    /** {@inheritDoc} */
    public function aggregateId(): AggregateId
    {
        return $this->aggregateId;
    }

    public function playhead(): int
    {
        return $this->playhead;
    }

    /** {@inheritDoc} */
    public static function reconstituteFromEventStream(EventStream $aggregateHistory): RecordsEvents
    {
        $instance = new static(aggregateId: $aggregateHistory->aggregateId());
        $instance->replay(history: $aggregateHistory);

        return $instance;
    }
}
