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

namespace Codefy\Domain\Aggregate;

use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\Domain\EventSourcing\DomainEvents;

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
                throw new CorruptEventStreamException();
            }
        }

        parent::__construct(events: $events);
    }

    public function aggregateId(): AggregateId
    {
        return $this->aggregateId;
    }
}
