<?php

declare(strict_types=1);

namespace Codefy\Tests;

use Codefy\Domain\EventSourcing\BaseProjection;

use function json_encode;

use const JSON_PRETTY_PRINT;

final class InMemoryPostProjection extends BaseProjection implements PostProjection
{
    public function __construct(/** Database Connection */)
    {
    }

    /** {@inheritDoc} */
    public function projectWhenPostWasCreated(PostWasCreated $event): array
    {
        return [
            'eventId' => $event->eventId(),
            'aggregateId' => $event->aggregateId(),
            'eventType' => $event->eventType(),
            'playhead' => $event->playhead(),
            'payload' => json_encode(value: $event->payload(), flags: JSON_PRETTY_PRINT),
            'metadata' => json_encode(value: $event->metadata(), flags: JSON_PRETTY_PRINT),
            'recordedAt' => $event->recordedAt()
        ];
    }

    /** {@inheritDoc} */
    public function projectWhenTitleWasChanged(TitleWasChanged $event): array
    {
        return [
            'eventId' => $event->eventId(),
            'aggregateId' => $event->aggregateId(),
            'eventType' => $event->eventType(),
            'playhead' => $event->playhead(),
            'payload' => json_encode(value: $event->payload(), flags: JSON_PRETTY_PRINT),
            'metadata' => json_encode(value: $event->metadata(), flags: JSON_PRETTY_PRINT),
            'recordedAt' => $event->recordedAt()
        ];
    }
}
