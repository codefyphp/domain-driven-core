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
use Codefy\Domain\Aggregate\RecordsEvents;
use Codefy\Domain\EventSourcing\EventStore;
use Codefy\Domain\EventSourcing\Projection;

final class PostRepository implements AggregateRepository
{
    public function __construct(
        public readonly EventStore $eventStore,
        public readonly Projection $projection
    ) {
    }

    /** {@inheritDoc} */
    public function find(AggregateId $aggregateId): RecordsEvents
    {
        $aggregateHistory = $this->eventStore->getAggregateHistoryFor(aggregateId: $aggregateId);
        return Post::reconstitute(aggregateHistory: $aggregateHistory);
    }

    /** {@inheritDoc} */
    public function save(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();

        foreach ($events as $event) {
            $this->eventStore->append(event: $event);
        }
        $aggregate->clearRecordedEvents();

        $this->projection->project(events: $events);
    }
}
