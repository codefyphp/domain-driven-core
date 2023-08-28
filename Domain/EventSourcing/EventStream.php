<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/domain-driven-core
 * @copyright  2022 Joshua Parker <joshua@joshuaparker.dev>
 * @copyright  2014 Mathias Verraes <mathias@verraes.net>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Codefy\Domain\EventSourcing;

use Codefy\Domain\Aggregate\AggregateId;

class EventStream extends DomainEvents
{
    /**
     * @throws CorruptEventStreamException
     */
    public function __construct(public readonly AggregateId $aggregateId, array $events)
    {
        /** @var DomainEvent $event */
        foreach ($events as $event) {
            if (!$event->aggregateId()->equals($aggregateId)) {
                throw new CorruptEventStreamException(message: 'Event stream is corrupted.');
            }
        }

        parent::__construct(events: $events);
    }

    public function aggregateId(): AggregateId
    {
        return $this->aggregateId;
    }
}
