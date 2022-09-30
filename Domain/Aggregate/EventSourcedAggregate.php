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

use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\DomainEvents;
use Codefy\Domain\EventSourcing\EventStream;
use Codefy\Traits\EventProducerAware;
use Codefy\Traits\EventSourcedAware;
use Codefy\Traits\PublisherAware;
use Qubus\Exception\Data\TypeException;

class EventSourcedAggregate implements AggregateRoot, IsEventSourced
{
    use EventProducerAware;
    use EventSourcedAware;
    use PublisherAware;

    private function __construct(public readonly AggregateId $aggregateId)
    {
    }

    final public static function root(AggregateId $aggregateId): static
    {
        return new static(aggregateId: $aggregateId);
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

    /** {@inheritDoc}
     * @throws TypeException
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

    /**
     * Aggregate root version.
     *
     * @return int
     */
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

    final public function equals(AggregateRoot $aggregateRoot): bool
    {
        return $this->aggregateId() === $aggregateRoot->aggregateId();
    }

    /**
     * Retrieves the class name.
     *
     * @return string
     */
    final public static function className(): string
    {
        return static::class;
    }
}
