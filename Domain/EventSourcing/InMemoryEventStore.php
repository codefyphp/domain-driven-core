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
use Codefy\Domain\Aggregate\CorruptEventStreamException;
use Codefy\Domain\Aggregate\EventStream;

use function array_filter;

final class InMemoryEventStore implements EventStore
{
    private array $events = [];

    public function append(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @throws CorruptEventStreamException
     */
    public function getAggregateHistoryFor(AggregateId $aggregateId): EventStream
    {
        return new EventStream(
            aggregateId: $aggregateId,
            events: array_filter(
                array: $this->events,
                callback: function (DomainEvent $event) use ($aggregateId) {
                    return $event->aggregateId()->equals($aggregateId);
                }
            )
        );
    }

    /**
     * @throws CorruptEventStreamException
     */
    public function loadFromPlayhead(AggregateId $aggregateId, int $playhead): EventStream
    {
        return new EventStream(
            aggregateId: $aggregateId,
            events: array_filter(
                array: $this->events,
                callback: function (DomainEvent $event) use ($aggregateId, $playhead) {
                    return $event->aggregateId()->equals($aggregateId) && $playhead <= $event->playhead();
                }
            )
        );
    }
}
