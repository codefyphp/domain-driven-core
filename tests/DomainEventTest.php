<?php

declare(strict_types=1);

use Codefy\Domain\EventSourcing\DomainEvents;
use Codefy\Domain\EventSourcing\EventId;
use Codefy\Domain\Metadata;
use Codefy\Tests\Content;
use Codefy\Tests\InMemoryPostProjection;
use Codefy\Tests\PostId;
use Codefy\Tests\PostWasCreated;
use Codefy\Tests\Title;
use Codefy\Tests\TitleWasChanged;
use PHPUnit\Framework\Assert;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\DateTime\QubusDateTimeImmutable;
use Qubus\Support\DateTime\QubusDateTimeZone;

try {
    $postId = PostId::fromNative('760b7c16-b28e-4d31-9f93-7a2f0d3a1c51');
} catch (TypeException $e) {
    return $e;
}

$aggregateRootId = new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f');
$eventId = new EventId(value: '01HADYD4FYEC7SY7DTTCKTMXVT');

$eventWithData = TitleWasChanged::withData(
    postId: $aggregateRootId,
    title: new Title('Aggregate Changed Title')
)->withAddedMetadata(key: Metadata::EVENT_ID, value: $eventId);

$eventFromArray = TitleWasChanged::fromArray(
    data: [
        'eventId' => $eventId,
        'eventType' => 'title-was-changed',
        'aggregateId' => $aggregateRootId,
        'payload' => [
            'title' => 'Aggregate Changed Title',
        ],
        'metadata' => [
            Metadata::AGGREGATE_ID => $aggregateRootId,
            Metadata::AGGREGATE_TYPE => 'post',
            Metadata::EVENT_ID => $eventId,
            Metadata::EVENT_TYPE => 'title-was-changed',
        ],
        'recordedAt' => new QubusDateTimeImmutable(time: 'now', tz: new QubusDateTimeZone(timezone: 'UTC')),
    ]
)
    ->withPlayhead(playhead: 5);

it('should return domain event with data.', function () use ($eventWithData, $eventId) {
    expect(value: $eventWithData->eventType())->toEqual(expected: 'title-was-changed')
        ->and(value: $eventWithData->metaParam(name: '__event_type'))->toEqual(expected: 'title-was-changed')
        ->and(value: $eventWithData->playhead())->toEqual(expected: 1)
        ->and(value: $eventWithData->metaParam(name: '__aggregate_playhead'))->toEqual(expected: 1)
        ->and(value: 'post')->toEqual(expected: $eventWithData->metaParam(name: '__aggregate_type'))
        ->and(value: $eventWithData->aggregateId()->__toString())->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and(value: $eventWithData->payload()['title'])->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventWithData->metadata())->toEqual(expected: $eventWithData->toArray()['metadata'])
        ->and(value: $eventWithData->eventId())->toEqual(
            expected: EventId::fromNative($eventWithData->eventId()->__toString())
        )
        ->and(value: $eventWithData->param(name: 'title'))->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventWithData->metaParam(name: '__aggregate_id'))->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and(value: $eventId->__toString())->toEqual(
            expected: EventId::fromString(
                eventId: $eventWithData->metaParam(name: '__event_id')->__toString()
            )->__toString()
        )
        ->and(value: $eventWithData->recordedAt())->toEqual($eventWithData->metaParam(name: '__recorded_at'));
});

it('should return domain event from array.', function () use ($eventFromArray, $eventId) {
    expect(value: $eventFromArray->eventType())->toEqual(expected: 'title-was-changed')
        ->and(value: $eventFromArray->playhead())->toEqual(expected: 5)
        ->and(value: $eventFromArray->aggregateId()->__toString())->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and(value: $eventFromArray->payload()['title'])->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventFromArray->metadata())->toEqual(expected: $eventFromArray->toArray()['metadata'])
        ->and(value: $eventFromArray->eventId()->__toString())->toEqual(
            expected: EventId::fromNative($eventFromArray->eventId()->__toString())
        )
        ->and(value: $eventFromArray->param(name: 'title'))->toEqual(expected: 'Aggregate Changed Title')
        ->and(value: $eventFromArray->metaParam(name: '__aggregate_id'))->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        )
        ->and($eventFromArray->toArray())->toEqual(
            expected: [
                'eventType' => 'title-was-changed',
                'payload' => [
                    'title' => 'Aggregate Changed Title',
                ],
                'metadata' => [
                    '__aggregate_id' => new PostId(value: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'),
                    '__aggregate_playhead' => 5,
                    '__event_id' => new EventId(value: $eventId->__toString()),
                    '__event_type' => 'title-was-changed',
                    '__aggregate_type' => 'post',
                    '__recorded_at' => $eventFromArray->recordedAt(),
                ],
                'recordedAt' => $eventFromArray->recordedAt(),
                'eventId' => new EventId(value: $eventId->__toString()),
            ]
        );
});

it(
    'should return `__aggregate_id` value: 1cf57c2c-5c82-45a0-8a42-f0b725cfc42f.',
    function () use ($eventFromArray) {
        expect(value: $eventFromArray->metaParam(name: '__aggregate_id'))->toEqual(
            expected: '1cf57c2c-5c82-45a0-8a42-f0b725cfc42f'
        );
    }
);

it('should return `__aggregate_type` value: post.', function () use ($eventFromArray) {
    expect(value: $eventFromArray->metaParam(name: '__aggregate_type'))->toEqual(
        expected: 'post'
    );
});

it('should return `__aggregate_playhead` value: 1.', function () use ($eventFromArray) {
    expect(value: $eventFromArray->metaParam(name: '__aggregate_playhead'))->toEqual(
        expected: 5
    );
});

it(sprintf('should return `__event_id` value: %s.', $eventId->__toString()), function () use ($eventFromArray) {
    expect(value: (new EventId($eventFromArray->eventId()->__toString()))->__toString())->toEqual(
        expected: $eventFromArray->metaParam(name: '__event_id')
    );
});

it('should return `__event_type` value: title-was-changed.', function () use ($eventFromArray) {
    expect(value: 'title-was-changed')->toEqual(expected: $eventFromArray->metaParam(name: '__event_type'))
        ->and(value: 'title-was-changed')->toEqual(expected: $eventFromArray->eventType());
});

it('should return expected occuredOn value.', function () use ($eventFromArray) {
    expect(value: $eventFromArray->recordedAt())->toEqual(expected: $eventFromArray->metaParam(name: '__recorded_at'));
});

it('should generate eventType automatically to be: title-was-changed.', function () {
    $event = TitleWasChanged::withData(
        postId: new PostId(),
        title: new Title(
            value: 'Automatically Generate Event Type'
        )
    );
    expect(value: $event->eventType())->toEqual(expected: 'title-was-changed')
        ->and(value: $event->metaParam(name: '__event_type'))->toEqual(expected: 'title-was-changed');
});

it('should return all events when traversing.', function () use ($postId) {
    $post = PostWasCreated::withData(
        postId: $postId,
        title: new Title(value: 'New Post Title'),
        content: new Content(value: 'Short content for this new post.')
    );

    $title = TitleWasChanged::withData(
        postId: new PostId($postId->__toString()),
        title: new Title(
            value: 'Automatically Generate Event Type'
        )
    );
    $expected = [$post, $title];
    $eventStream = DomainEvents::fromArray($expected);

    $events = [];
    foreach ($eventStream as $event) {
        $events[] = $event;
    }

    Assert::assertEquals(expected: $expected, actual: $events);
});

it('should project when TitleWasChanged event is persisted.', function () use ($postId) {
    $title = TitleWasChanged::withData(
        postId: new PostId($postId->__toString()),
        title: new Title(
            value: 'Automatically Generate Event Type'
        )
    );

    $memory = new InMemoryPostProjection();
    $actual = $memory->projectWhenTitleWasChanged($title);

    $expected = [
        'eventId' => $title->eventId(),
        'aggregateId' => $title->aggregateId(),
        'eventType' => $title->eventType(),
        'playhead' => $title->playhead(),
        'payload' => json_encode(value: $title->payload(), flags: JSON_PRETTY_PRINT),
        'metadata' => json_encode(value: $title->metadata(), flags: JSON_PRETTY_PRINT),
        'recordedAt' => $title->recordedAt()
    ];

    Assert::assertEquals(expected: $expected, actual: $actual);
});
