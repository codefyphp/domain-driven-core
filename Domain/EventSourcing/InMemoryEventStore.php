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

use Codefy\Domain\Aggregate\AggregateId;
use Qubus\Exception\Data\TypeException;

use function array_filter;

final class InMemoryEventStore implements EventStore
{
    private array $events = [];

    public function append(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @throws TypeException
     */
    public function commit(DomainEvent ...$events): Transactional
    {
        $stream = DomainEvents::fromArray($events);
        $transactionId = new TransactionId();

        if (count($events) === 0) {
            return new EventStoreTransaction($transactionId, $stream, $events);
        }

        foreach ($events as $event) {
            $this->append($event);
        }

        return new EventStoreTransaction($transactionId, $stream, $events);
    }

    /**
     * @throws CorruptEventStreamException|EventStreamIsEmptyException
     */
    public function getAggregateHistoryFor(AggregateId $aggregateId): EventStream
    {
        $eventStream = new EventStream(
            aggregateId: $aggregateId,
            events: array_filter(
                array: $this->events,
                callback: function (DomainEvent $event) use ($aggregateId) {
                    return $event->aggregateId()->equals($aggregateId);
                }
            )
        );

        if ($eventStream->isEmpty()) {
            throw new EventStreamIsEmptyException(
                message: 'The requested event stream is empty.'
            );
        }

        return $eventStream;
    }

    /**
     * @throws CorruptEventStreamException|EventStreamIsEmptyException
     */
    public function loadFromPlayhead(AggregateId $aggregateId, int $playhead): EventStream
    {
        $eventStream = new EventStream(
            aggregateId: $aggregateId,
            events: array_filter(
                array: $this->events,
                callback: function (DomainEvent $event) use ($aggregateId, $playhead) {
                    return $event->aggregateId()->equals($aggregateId) && $playhead <= $event->playhead();
                }
            )
        );

        if ($eventStream->isEmpty()) {
            throw new EventStreamIsEmptyException(
                message: 'The requested event stream is empty.'
            );
        }

        return $eventStream;
    }
}
